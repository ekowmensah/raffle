<?php

namespace App\Controllers\Api;

class CampaignController extends ApiController
{
    private $campaignModel;
    private $stationModel;
    private $programmeModel;
    
    public function __construct()
    {
        parent::__construct();
        require_once '../app/models/Campaign.php';
        require_once '../app/models/Station.php';
        require_once '../app/models/Programme.php';
        $this->campaignModel = new \App\Models\Campaign();
        $this->stationModel = new \App\Models\Station();
        $this->programmeModel = new \App\Models\Programme();
    }
    
    /**
     * Get all active campaigns
     * GET /api/campaigns
     */
    public function index()
    {
        $stationId = $_GET['station_id'] ?? null;
        $programmeId = $_GET['programme_id'] ?? null;
        
        $campaigns = $this->campaignModel->getActiveCampaigns($stationId, $programmeId);
        
        $data = [];
        foreach ($campaigns as $campaign) {
            $data[] = $this->formatCampaign($campaign);
        }
        
        $this->success($data);
    }
    
    /**
     * Get campaign by ID
     * GET /api/campaigns/{id}
     */
    public function show($id)
    {
        $campaign = $this->campaignModel->find($id);
        
        if (!$campaign) {
            $this->error('Campaign not found', 404);
        }
        
        if ($campaign->status != 'active') {
            $this->error('Campaign is not active', 400);
        }
        
        $this->success($this->formatCampaignDetail($campaign));
    }
    
    /**
     * Get stations
     * GET /api/stations
     */
    public function stations()
    {
        $stations = $this->stationModel->getActive();
        
        $data = [];
        foreach ($stations as $station) {
            $data[] = [
                'id' => $station->id,
                'name' => $station->name,
                'code' => $station->code,
                'description' => $station->description
            ];
        }
        
        $this->success($data);
    }
    
    /**
     * Get programmes by station
     * GET /api/stations/{stationId}/programmes
     */
    public function programmes($stationId)
    {
        $programmes = $this->programmeModel->getByStation($stationId);
        
        $data = [];
        foreach ($programmes as $programme) {
            if ($programme->is_active) {
                $data[] = [
                    'id' => $programme->id,
                    'name' => $programme->name,
                    'station_id' => $programme->station_id,
                    'description' => $programme->description
                ];
            }
        }
        
        $this->success($data);
    }
    
    /**
     * Get campaigns by station and programme
     * GET /api/stations/{stationId}/programmes/{programmeId}/campaigns
     */
    public function campaignsByProgramme($stationId, $programmeId)
    {
        $db = new \App\Core\Database();
        
        $db->query("SELECT rc.*, s.name as sponsor_name
                   FROM raffle_campaigns rc
                   LEFT JOIN sponsors s ON rc.sponsor_id = s.id
                   INNER JOIN campaign_programme_access cpa ON rc.id = cpa.campaign_id
                   INNER JOIN programmes p ON cpa.programme_id = p.id
                   WHERE p.station_id = :station_id 
                   AND cpa.programme_id = :programme_id
                   AND rc.status = 'active'
                   AND rc.end_date >= CURDATE()
                   ORDER BY rc.name");
        $db->bind(':station_id', $stationId);
        $db->bind(':programme_id', $programmeId);
        $campaigns = $db->resultSet();
        
        $data = [];
        foreach ($campaigns as $campaign) {
            $data[] = $this->formatCampaign($campaign);
        }
        
        $this->success($data);
    }
    
    /**
     * Format campaign for list view
     */
    private function formatCampaign($campaign)
    {
        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'description' => $campaign->description ?? null,
            'ticket_price' => (float)$campaign->ticket_price,
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
            'status' => $campaign->status,
            'is_active' => $campaign->status == 'active',
            'sponsor' => [
                'id' => $campaign->sponsor_id,
                'name' => $campaign->sponsor_name ?? null
            ]
        ];
    }
    
    /**
     * Format campaign detail view
     */
    private function formatCampaignDetail($campaign)
    {
        $db = new \App\Core\Database();
        
        // Get total tickets sold
        $db->query("SELECT COUNT(*) as total FROM tickets WHERE campaign_id = :id");
        $db->bind(':id', $campaign->id);
        $ticketCount = $db->single();
        
        // Get prize pool
        $db->query("SELECT SUM(winner_pool_amount_total) as prize_pool 
                   FROM revenue_allocations WHERE campaign_id = :id");
        $db->bind(':id', $campaign->id);
        $prizeData = $db->single();
        
        // Get upcoming draws
        $db->query("SELECT * FROM draws 
                   WHERE campaign_id = :id 
                   AND draw_date >= CURDATE() 
                   ORDER BY draw_date ASC 
                   LIMIT 3");
        $db->bind(':id', $campaign->id);
        $upcomingDraws = $db->resultSet();
        
        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'description' => $campaign->description ?? null,
            'ticket_price' => (float)$campaign->ticket_price,
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
            'status' => $campaign->status,
            'is_active' => $campaign->status == 'active',
            'total_tickets_sold' => (int)$ticketCount->total,
            'current_prize_pool' => (float)($prizeData->prize_pool ?? 0),
            'sponsor' => [
                'id' => $campaign->sponsor_id,
                'name' => $campaign->sponsor_name ?? null,
                'logo' => $campaign->sponsor_logo ?? null
            ],
            'revenue_sharing' => [
                'platform_percent' => (float)$campaign->platform_percent,
                'station_percent' => (float)$campaign->station_percent,
                'programme_percent' => (float)$campaign->programme_percent,
                'prize_pool_percent' => (float)$campaign->prize_pool_percent
            ],
            'upcoming_draws' => array_map(function($draw) {
                return [
                    'id' => $draw->id,
                    'draw_date' => $draw->draw_date,
                    'draw_type' => $draw->draw_type,
                    'status' => $draw->status
                ];
            }, $upcomingDraws)
        ];
    }
}
