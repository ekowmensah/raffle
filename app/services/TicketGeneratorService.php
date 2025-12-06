<?php

namespace App\Services;

class TicketGeneratorService
{
    private $ticketModel;
    private $campaignModel;
    private $stationModel;

    public function __construct()
    {
        require_once '../app/models/Ticket.php';
        require_once '../app/models/Campaign.php';
        require_once '../app/models/Station.php';
        
        $this->ticketModel = new \App\Models\Ticket();
        $this->campaignModel = new \App\Models\Campaign();
        $this->stationModel = new \App\Models\Station();
    }

    public function generateTickets($paymentData)
    {
        $campaign = $this->campaignModel->findById($paymentData['campaign_id']);
        $station = $this->stationModel->findById($paymentData['station_id']);
        
        if (!$campaign || !$station) {
            return false;
        }

        // Calculate number of tickets based on amount and ticket price
        $ticketCount = floor($paymentData['amount'] / $campaign->ticket_price);
        
        if ($ticketCount < 1) {
            return false;
        }

        // Generate ONE ticket with quantity representing all entries
        $sequence = $this->ticketModel->getNextSequence($campaign->id, $station->id);
        
        $ticketCode = $this->ticketModel->generateTicketCode(
            $campaign->code,
            $station->short_code_label,
            $sequence
        );

        $ticketData = [
            'campaign_id' => $campaign->id,
            'player_id' => $paymentData['player_id'],
            'payment_id' => $paymentData['payment_id'],
            'station_id' => $station->id,
            'programme_id' => $paymentData['programme_id'] ?? $station->id,
            'ticket_code' => $ticketCode,
            'quantity' => $ticketCount  // Store quantity instead of creating multiple tickets
        ];

        // Create single ticket
        $ticketId = $this->ticketModel->create($ticketData);
        
        if ($ticketId) {
            return [
                'success' => true,
                'ticket_count' => $ticketCount,
                'tickets' => [$ticketData]  // Return as array for compatibility
            ];
        }

        return false;
    }

    public function verifyTicket($ticketCode)
    {
        $ticket = $this->ticketModel->findByCode($ticketCode);
        
        if (!$ticket) {
            return [
                'valid' => false,
                'message' => 'Invalid ticket code'
            ];
        }

        // Check if ticket has won in any draw
        require_once '../app/models/DrawWinner.php';
        $winnerModel = new \App\Models\DrawWinner();
        $win = $winnerModel->findByTicket($ticket->id);

        $prizeAmount = ($win && isset($win->prize_amount)) ? floatval($win->prize_amount) : 0;

        return [
            'valid' => true,
            'ticket' => $ticket,
            'is_winner' => ($win !== null && $win !== false && $prizeAmount > 0),
            'prize_amount' => $prizeAmount
        ];
    }

    public function generateBulkTickets($campaignId, $stationId, $programmeId, $playerId, $count)
    {
        $campaign = $this->campaignModel->findById($campaignId);
        $station = $this->stationModel->findById($stationId);
        
        if (!$campaign || !$station) {
            return false;
        }

        $tickets = [];
        $sequence = $this->ticketModel->getNextSequence($campaignId, $stationId);

        for ($i = 0; $i < $count; $i++) {
            $ticketCode = $this->ticketModel->generateTicketCode(
                $campaign->code,
                $station->short_code_label,
                $sequence + $i
            );

            $tickets[] = [
                'campaign_id' => $campaignId,
                'player_id' => $playerId,
                'payment_id' => null,
                'station_id' => $stationId,
                'programme_id' => $programmeId,
                'ticket_code' => $ticketCode,
                'ticket_price' => $campaign->ticket_price,
                'is_winner' => 0
            ];
        }

        return $this->ticketModel->bulkCreate($tickets);
    }
}
