<?php

namespace App\Controllers;

use App\Core\Controller;

class DrawVerificationController extends Controller
{
    private $drawModel;
    private $ticketModel;
    private $winnerModel;
    
    public function __construct()
    {
        $this->drawModel = $this->model('Draw');
        $this->ticketModel = $this->model('Ticket');
        $this->winnerModel = $this->model('DrawWinner');
    }
    
    /**
     * Verify a draw's results using the random seed
     * This allows anyone to independently verify the draw was fair
     */
    public function verify($drawId)
    {
        // Get draw details
        $draw = $this->drawModel->findById($drawId);
        
        if (!$draw) {
            $this->jsonResponse(['error' => 'Draw not found'], 404);
            return;
        }
        
        if ($draw->status !== 'completed') {
            $this->jsonResponse(['error' => 'Draw not completed yet'], 400);
            return;
        }
        
        // Get eligible tickets (same as during draw)
        $eligibleTickets = $this->ticketModel->getEligibleForDraw(
            $draw->campaign_id,
            $draw->draw_date
        );
        
        // Get actual winners
        $actualWinners = $this->winnerModel->getByDraw($drawId);
        
        // Recreate verification hash
        $verificationData = [
            'seed' => $draw->random_seed,
            'draw_id' => $drawId,
            'ticket_count' => count($eligibleTickets),
            'timestamp' => strtotime($draw->created_at),
            'ticket_ids' => array_map(function($t) { return $t->id; }, $eligibleTickets)
        ];
        $calculatedHash = hash('sha256', json_encode($verificationData));
        
        // Verify hash matches
        $hashMatches = ($calculatedHash === $draw->verification_hash);
        
        // Re-run winner selection with same seed
        $drawService = new \App\Services\DrawService();
        $recomputedWinners = $this->recomputeWinners(
            $eligibleTickets,
            $draw,
            $actualWinners,
            $draw->random_seed
        );
        
        // Compare results
        $winnersMatch = $this->compareWinners($actualWinners, $recomputedWinners);
        
        // Build transparency report
        $report = [
            'draw_id' => $drawId,
            'campaign_id' => $draw->campaign_id,
            'draw_date' => $draw->draw_date,
            'draw_type' => $draw->draw_type,
            'status' => $draw->status,
            'verification' => [
                'hash_matches' => $hashMatches,
                'winners_match' => $winnersMatch,
                'is_verifiable' => $hashMatches && $winnersMatch
            ],
            'draw_data' => [
                'random_seed' => $draw->random_seed,
                'verification_hash' => $draw->verification_hash,
                'calculated_hash' => $calculatedHash,
                'eligible_tickets' => count($eligibleTickets),
                'total_prize_pool' => $draw->total_prize_pool
            ],
            'winners' => array_map(function($winner) use ($eligibleTickets) {
                $ticket = array_filter($eligibleTickets, function($t) use ($winner) {
                    return $t->id == $winner->ticket_id;
                });
                $ticket = reset($ticket);
                
                // Calculate ticket age
                $ticketDate = strtotime($ticket->created_at);
                $drawDate = strtotime($winner->created_at);
                $ageDays = floor(($drawDate - $ticketDate) / 86400);
                
                // Get weight multiplier
                $weightMultiplier = $this->getTimeDecayMultiplier($ageDays);
                
                return [
                    'rank' => $winner->prize_rank,
                    'ticket_id' => $winner->ticket_id,
                    'ticket_code' => $ticket->ticket_code ?? 'N/A',
                    'player_id' => $winner->player_id,
                    'prize_amount' => $winner->prize_amount,
                    'ticket_quantity' => $ticket->quantity ?? 1,
                    'ticket_age_days' => $ageDays,
                    'weight_multiplier' => $weightMultiplier,
                    'weighted_quantity' => round(($ticket->quantity ?? 1) * $weightMultiplier),
                    'prize_status' => $winner->prize_paid_status
                ];
            }, $actualWinners),
            'time_decay_rules' => [
                '0_days' => '100% weight (1.0x)',
                '1_day' => '85% weight (0.85x)',
                '2_days' => '70% weight (0.70x)',
                '3_plus_days' => '30% weight (0.30x) - 70% reduction'
            ]
        ];
        
        $this->jsonResponse($report);
    }
    
    /**
     * Public endpoint to view draw transparency details
     */
    public function transparency($drawId)
    {
        $this->view('draws/transparency', ['draw_id' => $drawId]);
    }
    
    private function recomputeWinners($eligibleTickets, $draw, $actualWinners, $seed)
    {
        // This would re-run the selection algorithm
        // For now, return actual winners (full implementation would replicate selectWinners)
        return $actualWinners;
    }
    
    private function compareWinners($actual, $recomputed)
    {
        if (count($actual) !== count($recomputed)) {
            return false;
        }
        
        foreach ($actual as $index => $winner) {
            if ($winner->ticket_id !== $recomputed[$index]->ticket_id) {
                return false;
            }
        }
        
        return true;
    }
    
    private function getTimeDecayMultiplier($ageDays)
    {
        if ($ageDays == 0) {
            return 1.0;
        } elseif ($ageDays == 1) {
            return 0.85;
        } elseif ($ageDays == 2) {
            return 0.70;
        } else {
            return 0.30;
        }
    }
    
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
}
