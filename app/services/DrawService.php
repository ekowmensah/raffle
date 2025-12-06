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

        // Calculate prize pool up to draw date
        $prizePool = $this->drawModel->calculatePrizePool($draw->campaign_id, $draw->draw_type, $draw->draw_date);

        if ($prizePool <= 0) {
            return ['success' => false, 'message' => 'Insufficient prize pool'];
        }

        // Generate random seed for transparency
        $randomSeed = $this->generateRandomSeed();
        
        // Update draw with seed and eligible count
        $this->drawModel->update($drawId, [
            'total_prize_pool' => $prizePool
        ]);

        // Select winners based on draw configuration
        $winners = $this->selectWinners($eligibleTickets, $draw, $prizePool, $randomSeed);

        if (empty($winners)) {
            return ['success' => false, 'message' => 'Failed to select winners'];
        }

        // Save winners
        foreach ($winners as $winner) {
            $winnerId = $this->winnerModel->create($winner);
            
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
                $this->smsService->sendWinnerNotification(
                    $player->phone,
                    $ticket->ticket_code,
                    $winner['prize_amount'],
                    $prizeRank,
                    $campaign->name
                );
            }
        }

        // Update draw status
        $this->drawModel->updateStatus($drawId, 'completed');

        return [
            'success' => true,
            'message' => 'Draw completed successfully',
            'winner_count' => count($winners),
            'total_prizes' => array_sum(array_column($winners, 'prize_amount'))
        ];
    }

    private function selectWinners($eligibleTickets, $draw, $prizePool, $seed)
    {
        $winners = [];
        $winnerCount = $draw->winner_count ?? 1;
        
        // Seed random number generator for reproducibility
        mt_srand(crc32($seed));
        
        // Expand tickets by quantity for fair odds
        // Each ticket with quantity=100 gets 100 entries in the pool
        $expandedTickets = [];
        foreach ($eligibleTickets as $ticket) {
            $quantity = $ticket->quantity ?? 1;
            for ($i = 0; $i < $quantity; $i++) {
                $expandedTickets[] = $ticket;
            }
        }
        
        // Shuffle expanded ticket pool
        shuffle($expandedTickets);
        
        // Calculate prize distribution
        $prizeDistribution = $this->calculatePrizeDistribution($prizePool, $winnerCount);
        
        // Select winners (ensuring unique tickets)
        $selectedTicketIds = [];
        $winnerIndex = 0;
        
        for ($i = 0; $i < count($expandedTickets) && $winnerIndex < $winnerCount; $i++) {
            $winningTicket = $expandedTickets[$i];
            
            // Skip if this ticket already won (one ticket can only win once)
            if (in_array($winningTicket->id, $selectedTicketIds)) {
                continue;
            }
            
            $selectedTicketIds[] = $winningTicket->id;
            
            $winners[] = [
                'draw_id' => $draw->id,
                'ticket_id' => $winningTicket->id,
                'player_id' => $winningTicket->player_id,
                'prize_amount' => $prizeDistribution[$winnerIndex],
                'prize_rank' => $winnerIndex + 1,
                'prize_paid_status' => 'pending'
            ];
            
            $winnerIndex++;
        }
        
        // Reset random seed
        mt_srand();
        
        return $winners;
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

        // Check if there's enough prize pool available (filtered by station/programme)
        $availablePrizePool = $this->drawModel->calculatePrizePool($campaignId, $drawType, $drawDate, $stationId, $programmeId);
        
        if ($availablePrizePool <= 0) {
            return [
                'error' => true,
                'message' => 'Insufficient prize pool for this draw. The prize pool for ' . $drawType . ' draws has been exhausted for this station/programme combination. Please wait for more ticket sales or check revenue allocations.'
            ];
        }

        $data = [
            'campaign_id' => $campaignId,
            'station_id' => $stationId,
            'programme_id' => $programmeId,
            'draw_type' => $drawType,
            'draw_date' => $drawDate,
            'winner_count' => max(1, intval($winnerCount)),
            'status' => 'pending',
            'started_by_user_id' => $_SESSION['user_id'] ?? 1,
            'total_prize_pool' => $availablePrizePool
        ];

        try {
            // Log the data being inserted
            error_log("Creating draw with data: " . json_encode($data));
            
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
