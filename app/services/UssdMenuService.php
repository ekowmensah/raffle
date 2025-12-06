<?php

namespace App\Services;

class UssdMenuService
{
    private $db;
    private $sessionService;
    
    public function __construct()
    {
        $this->db = new \App\Core\Database();
        $this->sessionService = new UssdSessionService();
    }
    
    /**
     * Build main menu
     */
    public function buildMainMenu()
    {
        return "CON Welcome to Raffle System\n" .
               "1. Buy Ticket\n" .
               "2. Check My Tickets\n" .
               "3. Check Winners\n" .
               "4. My Balance\n" .
               "0. Exit";
    }
    
    /**
     * Build station selection menu
     */
    public function buildStationMenu()
    {
        $this->db->query("SELECT id, name FROM stations WHERE is_active = 1 ORDER BY name");
        $stations = $this->db->resultSet();
        
        $menu = "CON Select Station:\n";
        $index = 1;
        
        foreach ($stations as $station) {
            $menu .= "{$index}. {$station->name}\n";
            $index++;
        }
        
        $menu .= "0. Back";
        
        return $menu;
    }
    
    /**
     * Build programme selection menu
     */
    public function buildProgrammeMenu($stationId)
    {
        $this->db->query("SELECT id, name FROM programmes 
                         WHERE station_id = :station_id AND is_active = 1 
                         ORDER BY name");
        $this->db->bind(':station_id', $stationId);
        $programmes = $this->db->resultSet();
        
        if (empty($programmes)) {
            return "END No active programmes available for this station.";
        }
        
        $menu = "CON Select Programme:\n";
        $index = 1;
        
        foreach ($programmes as $programme) {
            $menu .= "{$index}. {$programme->name}\n";
            $index++;
        }
        
        $menu .= "0. Back";
        
        return $menu;
    }
    
    /**
     * Build campaign selection menu for station (includes station-wide campaigns)
     */
    public function buildStationCampaignMenu($stationId)
    {
        // Get station-wide campaigns only
        $this->db->query("SELECT rc.id, rc.name, rc.ticket_price, rc.currency, rc.end_date
                         FROM raffle_campaigns rc
                         WHERE rc.station_id = :station_id
                         AND rc.status = 'active'
                         AND rc.end_date >= CURDATE()
                         AND rc.id NOT IN (SELECT campaign_id FROM campaign_programme_access WHERE programme_id IS NOT NULL)
                         ORDER BY rc.name");
        $this->db->bind(':station_id', $stationId);
        $campaigns = $this->db->resultSet();
        
        $menu = "Select Campaign:\n";
        $index = 1;
        
        foreach ($campaigns as $campaign) {
            $menu .= "{$index}. {$campaign->name} ({$campaign->currency} {$campaign->ticket_price})\n";
            $index++;
        }
        
        // Add option to browse by programme
        $menu .= "{$index}. Browse by Programme\n";
        $menu .= "0. Back";
        
        return $menu;
    }
    
    /**
     * Build campaign selection menu for programme (programme-specific campaigns only)
     */
    public function buildCampaignMenu($stationId, $programmeId)
    {
        // Get programme-specific campaigns only (NOT station-wide)
        $this->db->query("SELECT DISTINCT rc.id, rc.name, rc.ticket_price, rc.currency, rc.end_date
                         FROM raffle_campaigns rc
                         INNER JOIN campaign_programme_access cpa ON rc.id = cpa.campaign_id
                         WHERE rc.station_id = :station_id
                         AND rc.status = 'active'
                         AND rc.end_date >= CURDATE()
                         AND cpa.programme_id = :programme_id
                         ORDER BY rc.name");
        $this->db->bind(':station_id', $stationId);
        $this->db->bind(':programme_id', $programmeId);
        $campaigns = $this->db->resultSet();
        
        if (empty($campaigns)) {
            return "No active campaigns available.";
        }
        
        $menu = "Select Campaign:\n";
        $index = 1;
        
        foreach ($campaigns as $campaign) {
            $menu .= "{$index}. {$campaign->name} (GHS " . number_format($campaign->ticket_price, 2) . ")\n";
            $index++;
        }
        
        $menu .= "0. Back";
        
        return $menu;
    }
    
    /**
     * Build ticket quantity menu
     */
    public function buildQuantityMenu($campaignName, $ticketPrice)
    {
        return "{$campaignName}\n" .
               "Ticket Price: GHS " . number_format($ticketPrice, 2) . "\n\n" .
               "How many tickets?\n" .
               "1. 1 ticket (GHS " . number_format($ticketPrice, 2) . ")\n" .
               "2. 2 tickets (GHS " . number_format($ticketPrice * 2, 2) . ")\n" .
               "3. 3 tickets (GHS " . number_format($ticketPrice * 3, 2) . ")\n" .
               "4. 5 tickets (GHS " . number_format($ticketPrice * 5, 2) . ")\n" .
               "5. Enter custom amount\n" .
               "0. Back";
    }
    
    /**
     * Build payment confirmation menu
     */
    public function buildPaymentConfirmation($quantity, $totalAmount, $phoneNumber)
    {
        return "Confirm Purchase:\n" .
               "Tickets: {$quantity}\n" .
               "Total: GHS " . number_format($totalAmount, 2) . "\n" .
               "Phone: {$phoneNumber}\n\n" .
               "1. Confirm & Pay\n" .
               "0. Cancel";
    }
    
    /**
     * Build payment method menu
     */
    public function buildPaymentMethodMenu()
    {
        return "Select Payment Method:\n" .
               "1. Mobile Money (All Networks)\n" .
               "2. Manual Payment (Test)\n" .
               "0. Cancel";
    }
    
    /**
     * Build ticket list for player
     */
    public function buildTicketList($phoneNumber)
    {
        $this->db->query("SELECT t.ticket_code, rc.name as campaign_name, t.created_at
                         FROM tickets t
                         INNER JOIN players p ON t.player_id = p.id
                         INNER JOIN raffle_campaigns rc ON t.campaign_id = rc.id
                         WHERE p.phone = :phone
                         ORDER BY t.created_at DESC
                         LIMIT 5");
        $this->db->bind(':phone', $phoneNumber);
        $tickets = $this->db->resultSet();
        
        if (empty($tickets)) {
            return "You have no tickets yet.";
        }
        
        $menu = "Your Recent Tickets:\n\n";
        
        foreach ($tickets as $ticket) {
            $menu .= "Code: {$ticket->ticket_code}\n";
            $menu .= "Campaign: {$ticket->campaign_name}\n";
            $menu .= "Date: " . date('d M Y', strtotime($ticket->created_at)) . "\n\n";
        }
        
        return $menu;
    }
    
    /**
     * Build winner check menu
     */
    public function buildWinnerCheck($phoneNumber)
    {
        $this->db->query("SELECT dw.prize_rank, dw.prize_amount, rc.name as campaign_name, 
                                d.draw_date, dw.prize_paid_status
                         FROM draw_winners dw
                         INNER JOIN tickets t ON dw.ticket_id = t.id
                         INNER JOIN players p ON t.player_id = p.id
                         INNER JOIN draws d ON dw.draw_id = d.id
                         INNER JOIN raffle_campaigns rc ON d.campaign_id = rc.id
                         WHERE p.phone = :phone
                         ORDER BY d.draw_date DESC
                         LIMIT 3");
        $this->db->bind(':phone', $phoneNumber);
        $winners = $this->db->resultSet();
        
        if (empty($winners)) {
            return "You haven't won yet. Keep playing!";
        }
        
        $menu = "Congratulations! You Won:\n\n";
        
        foreach ($winners as $winner) {
            $menu .= "Campaign: {$winner->campaign_name}\n";
            $menu .= "Prize: GHS " . number_format($winner->prize_amount, 2) . "\n";
            $menu .= "Rank: {$winner->prize_rank}\n";
            $menu .= "Status: " . strtoupper($winner->prize_paid_status) . "\n";
            $menu .= "Date: " . date('d M Y', strtotime($winner->draw_date)) . "\n\n";
        }
        
        return $menu;
    }
    
    /**
     * Build balance inquiry
     */
    public function buildBalanceInquiry($phoneNumber)
    {
        // Get player info
        $this->db->query("SELECT * FROM players WHERE phone = :phone");
        $this->db->bind(':phone', $phoneNumber);
        $player = $this->db->single();
        
        if (!$player) {
            return "No account found for this number.";
        }
        
        // Get ticket count
        $this->db->query("SELECT COUNT(*) as ticket_count FROM tickets WHERE player_id = :player_id");
        $this->db->bind(':player_id', $player->id);
        $ticketData = $this->db->single();
        
        // Get winnings
        $this->db->query("SELECT SUM(prize_amount) as total_winnings 
                         FROM draw_winners dw
                         INNER JOIN tickets t ON dw.ticket_id = t.id
                         WHERE t.player_id = :player_id");
        $this->db->bind(':player_id', $player->id);
        $winningData = $this->db->single();
        
        $menu = "Account Balance:\n\n";
        $menu .= "Name: {$player->name}\n";
        $menu .= "Phone: {$player->phone}\n";
        $menu .= "Total Tickets: {$ticketData->ticket_count}\n";
        $menu .= "Total Winnings: GHS " . number_format($winningData->total_winnings ?? 0, 2) . "\n";
        $menu .= "Loyalty Points: {$player->loyalty_points}";
        
        return $menu;
    }
    
    /**
     * Get stations as array for indexing
     */
    public function getStationsArray()
    {
        $this->db->query("SELECT id, name FROM stations WHERE is_active = 1 ORDER BY name");
        return $this->db->resultSet();
    }
    
    /**
     * Get programmes as array for indexing
     */
    public function getProgrammesArray($stationId)
    {
        $this->db->query("SELECT id, name FROM programmes 
                         WHERE station_id = :station_id AND is_active = 1 
                         ORDER BY name");
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }
    
    /**
     * Get station-wide campaigns as array for indexing
     */
    public function getStationCampaignsArray($stationId)
    {
        $this->db->query("SELECT rc.id, rc.name, rc.ticket_price, rc.currency
                         FROM raffle_campaigns rc
                         WHERE rc.station_id = :station_id
                         AND rc.status = 'active'
                         AND rc.end_date >= CURDATE()
                         AND rc.id NOT IN (SELECT campaign_id FROM campaign_programme_access WHERE programme_id IS NOT NULL)
                         ORDER BY rc.name");
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }
    
    /**
     * Get campaigns as array for indexing (programme-specific campaigns only)
     */
    public function getCampaignsArray($stationId, $programmeId)
    {
        $this->db->query("SELECT DISTINCT rc.id, rc.name, rc.ticket_price, rc.currency
                         FROM raffle_campaigns rc
                         INNER JOIN campaign_programme_access cpa ON rc.id = cpa.campaign_id
                         WHERE rc.station_id = :station_id
                         AND rc.status = 'active'
                         AND rc.end_date >= CURDATE()
                         AND cpa.programme_id = :programme_id
                         ORDER BY rc.name");
        $this->db->bind(':station_id', $stationId);
        $this->db->bind(':programme_id', $programmeId);
        return $this->db->resultSet();
    }
}
