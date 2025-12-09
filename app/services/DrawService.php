<?php

namespace App\Services;

class DrawService
{
    private $drawModel;
    private $ticketModel;
    private $winnerModel;
    private $campaignModel;
    private $smsService;

    public function __construct()
    {
        require_once '../app/models/Draw.php';
        require_once '../app/models/Ticket.php';
        require_once '../app/models/DrawWinner.php';
        require_once '../app/models/Campaign.php';
        require_once '../app/services/SMS/HubtelSmsService.php';
        
        $this->drawModel = new \App\Models\Draw();
        $this->ticketModel = new \App\Models\Ticket();
        $this->winnerModel = new \App\Models\DrawWinner();
        $this->campaignModel = new \App\Models\Campaign();
        $this->smsService = new \App\Services\SMS\HubtelSmsService();
    }

    public function conductDraw($drawId, $userId)
    {
        $draw = $this->drawModel->findById($drawId);
        
        if (!$draw || $draw->status !== 'pending') {
            return ['success' => false, 'message' => 'Invalid draw or already completed'];
        }

        $campaign = $this->campaignModel->findById($draw->campaign_id);
        
        if (!$campaign) {
            return ['success' => false, 'message' => 'Campaign not found'];
        }

        // Get eligible tickets
        $eligibleTickets = $this->ticketModel->getEligibleForDraw(
            $draw->campaign_id,
            $draw->draw_date
        );

        if (empty($eligibleTickets)) {
            return ['success' => false, 'message' => 'No eligible tickets for this draw'];
        }

        // === ITEM CAMPAIGN LOGIC ===
        if ($campaign->campaign_type === 'item') {
            return $this->conductItemDraw($draw, $campaign, $eligibleTickets);
        }

        // === CASH CAMPAIGN LOGIC ===
        // Calculate prize pool up to draw date
        $prizePool = $this->drawModel->calculatePrizePool($draw->campaign_id, $draw->draw_type, $draw->draw_date);

        if ($prizePool <= 0) {
            return ['success' => false, 'message' => 'Insufficient prize pool'];
        }

        // Generate random seed for transparency and verifiability
        $randomSeed = $this->generateRandomSeed();
        
        // Create verification hash (tickets + seed + timestamp)
        $verificationData = [
            'seed' => $randomSeed,
            'draw_id' => $drawId,
            'ticket_count' => count($eligibleTickets),
            'timestamp' => time(),
            'ticket_ids' => array_map(function($t) { return $t->id; }, $eligibleTickets)
        ];
        $verificationHash = hash('sha256', json_encode($verificationData));
        
        // Update draw with seed, hash, and eligible count
        $this->drawModel->update($drawId, [
            'total_prize_pool' => $prizePool,
            'random_seed' => $randomSeed,
            'verification_hash' => $verificationHash
        ]);

        // Select winners based on draw configuration
        $winners = $this->selectWinners($eligibleTickets, $draw, $prizePool, $randomSeed);

        if (empty($winners)) {
            return ['success' => false, 'message' => 'Failed to select winners'];
        }

        // Log draw execution details for transparency
        error_log("=== DRAW EXECUTION LOG ===");
        error_log("Draw ID: {$drawId}");
        error_log("Campaign: {$campaign->name}");
        error_log("Random Seed: {$randomSeed}");
        error_log("Verification Hash: {$verificationHash}");
        error_log("Eligible Tickets: " . count($eligibleTickets));
        error_log("Prize Pool: GHS {$prizePool}");
        error_log("Winners Selected: " . count($winners));
        
        // Save winners
        foreach ($winners as $winner) {
            // Separate DB data from metadata
            $dbData = [
                'draw_id' => $winner['draw_id'],
                'ticket_id' => $winner['ticket_id'],
                'player_id' => $winner['player_id'],
                'prize_amount' => $winner['prize_amount'],
                'prize_rank' => $winner['prize_rank'],
                'prize_paid_status' => $winner['prize_paid_status']
            ];
            
            $winnerId = $this->winnerModel->create($dbData);
            
            // Log winner details with time-decay info if available
            $logMsg = "Winner #{$winner['prize_rank']}: Ticket #{$winner['ticket_id']}, Prize: GHS {$winner['prize_amount']}";
            if (isset($winner['ticket_age_days'])) {
                $logMsg .= ", Ticket Age: {$winner['ticket_age_days']} days, Weight: {$winner['weight_multiplier']}x";
            }
            error_log($logMsg);
            
            // Mark ticket as winner (if column exists)
            // $this->ticketModel->markAsWinner(
            //     $winner['ticket_id'],
            //     $drawId,
            //     $winner['prize_amount']
            // );

            // Send SMS notification
            $ticket = $this->ticketModel->findById($winner['ticket_id']);
            $playerModel = new \App\Models\Player();
            $player = $playerModel->findById($winner['player_id']);
            
            if ($player) {
                $prizeRank = $this->getPrizeRankName($winner['prize_rank']);
                // Send cash winner notification
                $this->smsService->sendWinnerNotification(
                    $player->phone,
                    $ticket->ticket_code,
                    $winner['prize_amount'],
                    $prizeRank,
                    $campaign->name
                );
            }
        }
        
        error_log("=== DRAW COMPLETED ===");

        // Update draw status
        $this->drawModel->updateStatus($drawId, 'completed');

        return [
            'success' => true,
            'message' => 'Draw completed successfully',
            'winner_count' => count($winners),
            'total_prizes' => array_sum(array_column($winners, 'prize_amount'))
        ];
    }

    /**
     * Conduct draw for item campaigns
     */
    private function conductItemDraw($draw, $campaign, $eligibleTickets)
    {
        // Check minimum ticket threshold
        if ($campaign->min_tickets_for_draw && count($eligibleTickets) < $campaign->min_tickets_for_draw) {
            return [
                'success' => false, 
                'message' => "Minimum {$campaign->min_tickets_for_draw} tickets required. Only " . count($eligibleTickets) . " tickets sold."
            ];
        }
        
        // Determine number of winners based on selection type
        $winnerCount = $this->getItemWinnerCount($campaign);
        
        // Check inventory if tracking is enabled
        if (isset($campaign->track_inventory) && $campaign->track_inventory) {
            $itemsRemaining = ($campaign->items_total ?? 0) - ($campaign->items_awarded ?? 0);
            
            if ($itemsRemaining < $winnerCount) {
                return [
                    'success' => false,
                    'message' => "Insufficient inventory! Need {$winnerCount} items but only {$itemsRemaining} remaining. Total: {$campaign->items_total}, Already awarded: {$campaign->items_awarded}"
                ];
            }
            
            error_log("Inventory Check: Need {$winnerCount}, Available {$itemsRemaining}");
        }
        
        // Generate random seed for transparency
        $randomSeed = $this->generateRandomSeed();
        
        // Create verification hash
        $verificationData = [
            'seed' => $randomSeed,
            'draw_id' => $draw->id,
            'ticket_count' => count($eligibleTickets),
            'timestamp' => time(),
            'campaign_type' => 'item',
            'item_name' => $campaign->item_name,
            'winner_count' => $winnerCount,
            'ticket_ids' => array_map(function($t) { return $t->id; }, $eligibleTickets)
        ];
        $verificationHash = hash('sha256', json_encode($verificationData));
        
        // Update draw with item value for reference
        $this->drawModel->update($draw->id, [
            'total_prize_pool' => $campaign->item_value ?? 0,
            'random_seed' => $randomSeed,
            'verification_hash' => $verificationHash
        ]);
        
        // Select winners for item campaign
        $winners = $this->selectItemWinners($eligibleTickets, $draw, $campaign, $randomSeed);
        
        if (empty($winners)) {
            return ['success' => false, 'message' => 'Failed to select winners'];
        }
        
        // Log draw execution
        error_log("=== ITEM DRAW EXECUTION LOG ===");
        error_log("Draw ID: {$draw->id}");
        error_log("Campaign: {$campaign->name}");
        error_log("Item: {$campaign->item_name}");
        error_log("Item Value: GHS " . ($campaign->item_value ?? 0));
        error_log("Random Seed: {$randomSeed}");
        error_log("Verification Hash: {$verificationHash}");
        error_log("Eligible Tickets: " . count($eligibleTickets));
        error_log("Winners Selected: " . count($winners));
        error_log("Selection Type: {$campaign->winner_selection_type}");
        
        // Save winners with item details
        foreach ($winners as $winner) {
            $dbData = [
                'draw_id' => $winner['draw_id'],
                'ticket_id' => $winner['ticket_id'],
                'player_id' => $winner['player_id'],
                'prize_amount' => $winner['item_value'], // Store item value
                'prize_rank' => $winner['prize_rank'],
                'prize_paid_status' => 'pending' // Will track delivery
            ];
            
            $winnerId = $this->winnerModel->create($dbData);
            
            error_log("Winner #{$winner['prize_rank']}: Ticket #{$winner['ticket_id']}, Item Value: GHS {$winner['item_value']}");
            
            // Send SMS notification for item win
            $ticket = $this->ticketModel->findById($winner['ticket_id']);
            $playerModel = new \App\Models\Player();
            $player = $playerModel->findById($winner['player_id']);
            
            if ($player) {
                $this->sendItemWinnerNotification(
                    $player->phone,
                    $ticket->ticket_code,
                    $campaign->item_name,
                    $campaign->item_value,
                    $campaign->name,
                    $winner['prize_rank']
                );
            }
        }
        
        error_log("=== ITEM DRAW COMPLETED ===");
        
        // Update inventory - increment items_awarded
        if (isset($campaign->track_inventory) && $campaign->track_inventory) {
            $newItemsAwarded = ($campaign->items_awarded ?? 0) + count($winners);
            $this->campaignModel->update($campaign->id, [
                'items_awarded' => $newItemsAwarded
            ]);
            
            $itemsRemaining = ($campaign->items_total ?? 0) - $newItemsAwarded;
            error_log("Inventory Updated: Awarded {$newItemsAwarded}/{$campaign->items_total}, Remaining: {$itemsRemaining}");
        }
        
        // Update draw status
        $this->drawModel->updateStatus($draw->id, 'completed');
        
        return [
            'success' => true,
            'message' => 'Item draw completed successfully',
            'winner_count' => count($winners),
            'item_name' => $campaign->item_name,
            'item_value' => $campaign->item_value
        ];
    }

    /**
     * Determine number of winners for item campaign
     */
    private function getItemWinnerCount($campaign)
    {
        switch ($campaign->winner_selection_type) {
            case 'single':
                return 1;
            
            case 'multiple':
                return $campaign->item_quantity ?? 1;
            
            case 'tiered':
                // For tiered, default to 3 tiers (1st, 2nd, 3rd)
                return 3;
            
            default:
                return 1;
        }
    }

    /**
     * Select winners for item campaign
     */
    private function selectItemWinners($eligibleTickets, $draw, $campaign, $randomSeed)
    {
        $winnerCount = $this->getItemWinnerCount($campaign);
        
        // Ensure we don't try to select more winners than tickets
        $winnerCount = min($winnerCount, count($eligibleTickets));
        
        // Seed random number generator for reproducibility
        mt_srand(crc32($randomSeed));
        
        $winners = [];
        $selectedTickets = [];
        
        for ($rank = 1; $rank <= $winnerCount; $rank++) {
            // Get remaining tickets
            $availableTickets = array_filter($eligibleTickets, function($ticket) use ($selectedTickets) {
                return !in_array($ticket->id, $selectedTickets);
            });
            
            if (empty($availableTickets)) {
                break;
            }
            
            // Randomly select a ticket
            $randomIndex = mt_rand(0, count($availableTickets) - 1);
            $winningTicket = array_values($availableTickets)[$randomIndex];
            
            // Mark as selected
            $selectedTickets[] = $winningTicket->id;
            
            // Determine item value for this rank (for tiered)
            $itemValue = $campaign->item_value ?? 0;
            if ($campaign->winner_selection_type === 'tiered') {
                // Adjust value based on rank (1st gets full, 2nd gets 50%, 3rd gets 25%)
                $tierMultipliers = [1 => 1.0, 2 => 0.5, 3 => 0.25];
                $itemValue = $itemValue * ($tierMultipliers[$rank] ?? 0.1);
            }
            
            $winners[] = [
                'draw_id' => $draw->id,
                'ticket_id' => $winningTicket->id,
                'player_id' => $winningTicket->player_id,
                'item_value' => $itemValue,
                'prize_rank' => $rank
            ];
        }
        
        // Reset random seed
        mt_srand();
        
        return $winners;
    }

    /**
     * Send SMS notification for item winner
     */
    private function sendItemWinnerNotification($phone, $ticketCode, $itemName, $itemValue, $campaignName, $rank = 1)
    {
        $rankText = '';
        if ($rank == 1) {
            $rankText = '1st Prize: ';
        } elseif ($rank == 2) {
            $rankText = '2nd Prize: ';
        } elseif ($rank == 3) {
            $rankText = '3rd Prize: ';
        }
        
        $message = "🎉 CONGRATULATIONS! You won {$rankText}{$itemName} (worth GHS " . number_format($itemValue, 2) . ") in {$campaignName}! Ticket: {$ticketCode}. We'll contact you for delivery details.";
        
        return $this->smsService->send($phone, $message, 'item_winner');
    }

    private function selectWinners($eligibleTickets, $draw, $prizePool, $seed)
    {
        $winners = [];
        $winnerCount = $draw->winner_count ?? 3; // Use draw's winner count or default to 3
        
        if (empty($eligibleTickets)) {
            return [];
        }
        
        // Seed random number generator for reproducibility
        mt_srand(crc32($seed));
        
        // Apply time-decay weighting to older tickets
        $weightedTickets = $this->applyTimeDecayWeighting($eligibleTickets, $draw->draw_date);
        
        // Calculate total weighted entries
        $totalEntries = 0;
        foreach ($weightedTickets as $ticket) {
            $totalEntries += $ticket['weighted_quantity'];
        }
        
        if ($totalEntries == 0) {
            mt_srand();
            return [];
        }
        
        // Build cumulative probability ranges for weighted selection
        // This avoids memory-intensive array expansion
        $ranges = [];
        $cumulative = 0;
        foreach ($weightedTickets as $ticketData) {
            $weightedQty = $ticketData['weighted_quantity'];
            $ranges[] = [
                'ticket' => $ticketData['ticket'],
                'original_quantity' => $ticketData['original_quantity'],
                'weighted_quantity' => $weightedQty,
                'age_days' => $ticketData['age_days'],
                'weight_multiplier' => $ticketData['weight_multiplier'],
                'start' => $cumulative,
                'end' => $cumulative + $weightedQty - 1
            ];
            $cumulative += $weightedQty;
        }
        
        // Calculate prize distribution
        $prizeDistribution = $this->calculatePrizeDistribution($prizePool, $winnerCount);
        
        // Select winners using weighted random selection
        $selectedTicketIds = [];
        $winnerIndex = 0;
        $maxAttempts = min($totalEntries * 2, 10000); // Prevent infinite loop
        $attempts = 0;
        
        while ($winnerIndex < $winnerCount && $attempts < $maxAttempts) {
            $attempts++;
            
            // Generate random number in range [0, totalEntries)
            $randomNum = mt_rand(0, $totalEntries - 1);
            
            // Find which ticket this random number falls into
            foreach ($ranges as $range) {
                if ($randomNum >= $range['start'] && $randomNum <= $range['end']) {
                    $winningTicket = $range['ticket'];
                    
                    // Skip if this ticket already won (one ticket wins once per draw)
                    if (in_array($winningTicket->id, $selectedTicketIds)) {
                        continue 2; // Continue outer while loop
                    }
                    
                    $selectedTicketIds[] = $winningTicket->id;
                    
                    $winners[] = [
                        'draw_id' => $draw->id,
                        'ticket_id' => $winningTicket->id,
                        'player_id' => $winningTicket->player_id,
                        'prize_amount' => $prizeDistribution[$winnerIndex],
                        'prize_rank' => $winnerIndex + 1,
                        'prize_paid_status' => 'pending',
                        // Metadata for transparency (not saved to DB, just for logging)
                        'ticket_age_days' => $range['age_days'],
                        'weight_multiplier' => $range['weight_multiplier'],
                        'original_quantity' => $range['original_quantity'],
                        'weighted_quantity' => $range['weighted_quantity']
                    ];
                    
                    $winnerIndex++;
                    break; // Break inner foreach, continue while loop
                }
            }
        }
        
        // Reset random seed
        mt_srand();
        
        // Log selection details
        error_log("Winner selection completed: {$winnerIndex} winners from {$totalEntries} entries in {$attempts} attempts");
        
        return $winners;
    }

    /**
     * Apply time-decay weighting to tickets based on age
     * Older tickets get reduced winning chances (70% reduction for tickets 3+ days old)
     */
    private function applyTimeDecayWeighting($eligibleTickets, $drawDate)
    {
        $weightedTickets = [];
        
        foreach ($eligibleTickets as $ticket) {
            $ticketDate = date('Y-m-d', strtotime($ticket->created_at));
            $drawDateFormatted = date('Y-m-d', strtotime($drawDate));
            
            // Calculate age in days
            $ticketTimestamp = strtotime($ticketDate);
            $drawTimestamp = strtotime($drawDateFormatted);
            $ageDays = floor(($drawTimestamp - $ticketTimestamp) / 86400);
            
            // Apply time-decay multiplier
            $weightMultiplier = $this->getTimeDecayMultiplier($ageDays);
            
            $originalQty = $ticket->quantity ?? 1;
            $weightedQty = max(1, round($originalQty * $weightMultiplier)); // Minimum 1 entry
            
            $weightedTickets[] = [
                'ticket' => $ticket,
                'original_quantity' => $originalQty,
                'weighted_quantity' => $weightedQty,
                'age_days' => $ageDays,
                'weight_multiplier' => $weightMultiplier
            ];
        }
        
        return $weightedTickets;
    }
    
    /**
     * Get time-decay multiplier based on ticket age
     * 
     * Age 0 days (today):     100% weight (1.0)
     * Age 1 day:              85% weight (0.85)
     * Age 2 days:             70% weight (0.70)
     * Age 3+ days:            30% weight (0.30) - 70% reduction
     */
    private function getTimeDecayMultiplier($ageDays)
    {
        if ($ageDays == 0) {
            return 1.0;  // Today's tickets: 100% weight
        } elseif ($ageDays == 1) {
            return 0.85; // Yesterday: 85% weight (15% reduction)
        } elseif ($ageDays == 2) {
            return 0.70; // 2 days ago: 70% weight (30% reduction)
        } else {
            return 0.30; // 3+ days ago: 30% weight (70% reduction)
        }
    }

    private function calculatePrizeDistribution($totalPrize, $winnerCount)
    {
        $distribution = [];
        
        if ($winnerCount == 1) {
            $distribution[] = $totalPrize;
        } elseif ($winnerCount == 2) {
            // 60-40 split
            $distribution[] = round($totalPrize * 0.6, 2);
            $distribution[] = round($totalPrize * 0.4, 2);
        } elseif ($winnerCount == 3) {
            // 50-30-20 split
            $distribution[] = round($totalPrize * 0.5, 2);
            $distribution[] = round($totalPrize * 0.3, 2);
            $distribution[] = round($totalPrize * 0.2, 2);
        } else {
            // Equal distribution for more winners
            $prizePerWinner = round($totalPrize / $winnerCount, 2);
            for ($i = 0; $i < $winnerCount; $i++) {
                $distribution[] = $prizePerWinner;
            }
        }
        
        return $distribution;
    }

    private function generateRandomSeed()
    {
        return bin2hex(random_bytes(32));
    }
    
    private function getPrizeRankName($rank)
    {
        $ranks = [
            1 => '1st Prize',
            2 => '2nd Prize',
            3 => '3rd Prize'
        ];
        
        return $ranks[$rank] ?? $rank . 'th Prize';
    }

    public function scheduleDraw($campaignId, $drawType, $drawDate, $checkDuplicate = false, $winnerCount = 1, $stationId = null, $programmeId = null)
    {
        // Always check for duplicates to prevent database constraint errors
        $existing = $this->checkExistingDraw($campaignId, $drawType, $drawDate);
        
        if ($existing) {
            if ($checkDuplicate) {
                return false; // Silently skip for auto-scheduling
            } else {
                // For manual scheduling, return error info
                return [
                    'error' => true,
                    'message' => 'A ' . $drawType . ' draw already exists for this date. Please choose a different date or draw type.'
                ];
            }
        }

        // Get campaign to check type
        $campaign = $this->campaignModel->findById($campaignId);
        
        // Check if there's enough prize pool available (only for cash campaigns)
        if ($campaign->campaign_type === 'cash') {
            $availablePrizePool = $this->drawModel->calculatePrizePool($campaignId, $drawType, $drawDate, $stationId, $programmeId);
            
            if ($availablePrizePool <= 0) {
                return [
                    'error' => true,
                    'message' => 'Insufficient prize pool for this draw. The prize pool for ' . $drawType . ' draws has been exhausted for this station/programme combination. Please wait for more ticket sales or check revenue allocations.'
                ];
            }
        } else {
            // For item campaigns, set prize pool to 0 (not applicable)
            $availablePrizePool = 0;
        }

        $data = [
            'campaign_id' => $campaignId,
            'station_id' => $stationId,
            'programme_id' => $programmeId,
            'draw_type' => $drawType,
            'draw_date' => $drawDate,
            'status' => 'pending',
            'started_by_user_id' => $_SESSION['user_id'] ?? 1,
            'total_prize_pool' => $availablePrizePool,
            'winner_count' => $winnerCount
        ];

        try {
            // Log the data being inserted
            error_log("Creating draw with data: " . json_encode($data));
            error_log("Data keys: " . implode(', ', array_keys($data)));
            
            $drawId = $this->drawModel->create($data);
            
            if ($drawId) {
                error_log("Draw created successfully with ID: {$drawId}");
                return $drawId;
            } else {
                error_log("Draw creation returned false/null");
                return [
                    'error' => true,
                    'message' => 'Failed to create draw record. Please try again.'
                ];
            }
        } catch (\PDOException $e) {
            // Log the actual error
            error_log("PDO Exception in scheduleDraw: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            
            return [
                'error' => true,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            error_log("Exception in scheduleDraw: " . $e->getMessage());
            
            return [
                'error' => true,
                'message' => 'Failed to schedule draw: ' . $e->getMessage()
            ];
        }
    }

    public function scheduleAutoDailyDraws($campaignId)
    {
        $campaign = $this->campaignModel->findById($campaignId);
        
        if (!$campaign) {
            return ['success' => false, 'message' => 'Campaign not found'];
        }

        if (!$campaign->daily_draw_enabled) {
            return ['success' => false, 'message' => 'Daily draws not enabled for this campaign'];
        }

        $startDate = new \DateTime($campaign->start_date);
        $endDate = new \DateTime($campaign->end_date);
        
        // Schedule daily draws from start to one day before end
        $endDate->modify('-1 day');
        
        $drawsScheduled = 0;
        $drawsSkipped = 0;
        
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            
            // Check if draw already exists
            if (!$this->checkExistingDraw($campaignId, 'daily', $dateString)) {
                $result = $this->scheduleDraw($campaignId, 'daily', $dateString, true);
                
                if ($result) {
                    $drawsScheduled++;
                } else {
                    $drawsSkipped++;
                }
            } else {
                $drawsSkipped++;
            }
            
            $currentDate->modify('+1 day');
        }

        return [
            'success' => true,
            'message' => "Scheduled {$drawsScheduled} daily draw(s). {$drawsSkipped} already existed or skipped.",
            'scheduled' => $drawsScheduled,
            'skipped' => $drawsSkipped
        ];
    }

    private function checkExistingDraw($campaignId, $drawType, $drawDate)
    {
        // Use a temporary Draw model instance to check
        require_once '../app/core/Database.php';
        $db = new \App\Core\Database();
        
        $db->query("SELECT id FROM draws 
                   WHERE campaign_id = :campaign_id 
                   AND draw_type = :draw_type 
                   AND draw_date = :draw_date");
        $db->bind(':campaign_id', $campaignId);
        $db->bind(':draw_type', $drawType);
        $db->bind(':draw_date', $drawDate);
        
        return $db->single();
    }
}
