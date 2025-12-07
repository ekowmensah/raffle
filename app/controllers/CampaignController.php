<?php

namespace App\Controllers;

use App\Core\Controller;

class CampaignController extends Controller
{
    private $campaignModel;
    private $sponsorModel;
    private $programmeModel;
    private $accessModel;

    public function __construct()
    {
        $this->campaignModel = $this->model('Campaign');
        $this->sponsorModel = $this->model('Sponsor');
        $this->programmeModel = $this->model('Programme');
        $this->accessModel = $this->model('CampaignProgrammeAccess');
    }

    public function index()
    {
        $this->requireAuth();

        // Get campaigns based on user role
        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        if ($role === 'super_admin') {
            $campaigns = $this->campaignModel->getAllWithDetails();
        } elseif ($role === 'station_admin') {
            $campaigns = $this->campaignModel->getByStation($user->station_id);
        } elseif ($role === 'programme_manager') {
            $campaigns = $this->campaignModel->getByProgramme($user->programme_id);
        } elseif ($role === 'auditor') {
            // Auditors can view all
            $campaigns = $this->campaignModel->getAllWithDetails();
        } else {
            $campaigns = [];
        }

        $data = [
            'title' => 'Campaigns',
            'campaigns' => $campaigns
        ];

        $this->view('campaigns/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $campaign = $this->campaignModel->findById($id);

        if (!$campaign) {
            flash('error', 'Campaign not found');
            $this->redirect('campaign');
        }

        // Check if user can access this campaign
        if (!canAccessCampaign($campaign)) {
            flash('error', 'You do not have permission to view this campaign');
            $this->redirect('campaign');
        }

        $stats = $this->campaignModel->getStats($id);

        $data = [
            'title' => 'Campaign Details',
            'campaign' => $campaign,
            'stats' => $stats
        ];

        $this->view('campaigns/view', $data);
    }

    // Alias for show() method to support /campaign/viewDetails/{id} URLs
    public function viewDetails($id)
    {
        return $this->show($id);
    }

    public function create()
    {
        $this->requireAuth();

        // Check if user has permission to create campaigns
        if (!can('create_campaign')) {
            flash('error', 'You do not have permission to create campaigns');
            $this->redirect('campaign');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $stationId = $_POST['station_id'] ?? null;
            $programmeId = !empty($_POST['programme_id']) ? $_POST['programme_id'] : null;
            
            if (empty($stationId)) {
                flash('error', 'Station is required');
                $this->redirect('campaign/create');
                return;
            }
            
            $data = [
                'name' => sanitize($_POST['name']),
                'code' => sanitize($_POST['code']),
                'description' => sanitize($_POST['description']),
                'sponsor_id' => !empty($_POST['sponsor_id']) ? $_POST['sponsor_id'] : null,
                'station_id' => $stationId,
                'ticket_price' => $_POST['ticket_price'],
                'currency' => $_POST['currency'] ?? 'GHS',
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'status' => $_POST['status'] ?? 'draft',
                'platform_percent' => $_POST['platform_percent'] ?? 25,
                'station_percent' => $_POST['station_percent'] ?? 25,
                'programme_percent' => $_POST['programme_percent'] ?? 10,
                'prize_pool_percent' => $_POST['prize_pool_percent'] ?? 40,
                'daily_share_percent_of_pool' => $_POST['daily_share_percent_of_pool'] ?? 50,
                'final_share_percent_of_pool' => $_POST['final_share_percent_of_pool'] ?? 50,
                'daily_draw_enabled' => isset($_POST['daily_draw_enabled']) ? 1 : 0,
                'created_by_user_id' => $_SESSION['user_id']
            ];

            try {
                $campaignId = $this->campaignModel->create($data);

                if ($campaignId) {
                    // Create programme access only if programme is specified
                    if ($programmeId) {
                        $this->accessModel->addProgramme($campaignId, $programmeId);
                    }
                    
                    $campaignType = $programmeId ? 'programme-specific' : 'station-wide';
                    flash('success', 'Campaign created successfully as ' . $campaignType . ' campaign');
                    $this->redirect('campaign/show/' . $campaignId);
                } else {
                    flash('error', 'Failed to create campaign');
                    $_SESSION['old'] = $_POST;
                }
            } catch (\Exception $e) {
                flash('error', 'Error creating campaign: ' . $e->getMessage());
                $_SESSION['old'] = $_POST;
                $this->redirect('campaign/create');
            }
        }

        $sponsors = $this->sponsorModel->getActive();
        $stationModel = $this->model('Station');
        $stations = $stationModel->getAll();

        $data = [
            'title' => 'Create Campaign',
            'sponsors' => $sponsors,
            'stations' => $stations
        ];

        $this->view('campaigns/create', $data);
    }

    public function edit($id)
    {
        $this->requireAuth();

        $campaign = $this->campaignModel->findById($id);

        if (!$campaign) {
            flash('error', 'Campaign not found');
            $this->redirect('campaign');
        }

        // Check if user can edit this campaign
        if (!canEdit($campaign, 'campaign')) {
            flash('error', 'You do not have permission to edit this campaign');
            $this->redirect('campaign');
        }

        if ($campaign->is_config_locked) {
            flash('error', 'Campaign configuration is locked');
            $this->redirect('campaign/view/' . $id);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description']),
                'ticket_price' => $_POST['ticket_price'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'status' => $_POST['status'],
                'platform_percent' => $_POST['platform_percent'],
                'station_percent' => $_POST['station_percent'],
                'programme_percent' => $_POST['programme_percent'],
                'prize_pool_percent' => $_POST['prize_pool_percent'],
                'daily_draw_enabled' => isset($_POST['daily_draw_enabled']) ? 1 : 0
            ];

            if ($this->campaignModel->update($id, $data)) {
                flash('success', 'Campaign updated successfully');
                $this->redirect('campaign/show/' . $id);
            } else {
                flash('error', 'Failed to update campaign');
            }
        }

        $sponsors = $this->sponsorModel->getActive();

        $data = [
            'title' => 'Edit Campaign',
            'campaign' => $campaign,
            'sponsors' => $sponsors
        ];

        $this->view('campaigns/edit', $data);
    }

    public function dashboard()
    {
        $this->requireAuth();

        $activeCampaigns = $this->campaignModel->getActive();
        $draftCampaigns = $this->campaignModel->getByStatus('draft');

        $data = [
            'title' => 'Campaign Dashboard',
            'active_campaigns' => $activeCampaigns,
            'draft_campaigns' => $draftCampaigns
        ];

        $this->view('campaigns/dashboard', $data);
    }

    public function configureAccess($id)
    {
        $this->requireAuth();

        $campaign = $this->campaignModel->findById($id);

        if (!$campaign) {
            flash('error', 'Campaign not found');
            $this->redirect('campaign');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $programmeIds = $_POST['programme_ids'] ?? [];
            
            if ($this->accessModel->syncProgrammes($id, $programmeIds)) {
                flash('success', 'Programme access updated successfully');
                $this->redirect('campaign/show/' . $id);
            } else {
                flash('error', 'Failed to update programme access');
            }
        }

        $allProgrammes = $this->programmeModel->getAllWithStation();
        $assignedProgrammes = $this->accessModel->getByCampaign($id);
        $assignedIds = array_column($assignedProgrammes, 'programme_id');

        $data = [
            'title' => 'Configure Programme Access',
            'campaign' => $campaign,
            'all_programmes' => $allProgrammes,
            'assigned_ids' => $assignedIds
        ];

        $this->view('campaigns/configure-access', $data);
    }

    public function lock($id)
    {
        $this->requireAuth();

        if ($this->campaignModel->lockConfiguration($id)) {
            flash('success', 'Campaign configuration locked');
        } else {
            flash('error', 'Failed to lock configuration');
        }

        $this->redirect('campaign/show/' . $id);
    }

    public function unlock($id)
    {
        $this->requireAuth();

        if ($this->campaignModel->unlockConfiguration($id)) {
            flash('success', 'Campaign configuration unlocked');
        } else {
            flash('error', 'Failed to unlock configuration');
        }

        $this->redirect('campaign/show/' . $id);
    }

    public function clone($id)
    {
        $this->requireAuth();

        $campaign = $this->campaignModel->findById($id);

        if (!$campaign) {
            flash('error', 'Campaign not found');
            $this->redirect('campaign');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $newName = sanitize($_POST['name']);
            $newCode = sanitize($_POST['code']);

            // Check if code exists
            if ($this->campaignModel->findByCode($newCode)) {
                flash('error', 'Campaign code already exists');
                $this->redirect('campaign/clone/' . $id);
            }

            $newId = $this->campaignModel->cloneCampaign($id, $newName, $newCode);

            if ($newId) {
                // Clone programme access
                $programmes = $this->accessModel->getByCampaign($id);
                foreach ($programmes as $prog) {
                    $this->accessModel->addProgramme($newId, $prog->programme_id);
                }

                flash('success', 'Campaign cloned successfully');
                $this->redirect('campaign/show/' . $newId);
            } else {
                flash('error', 'Failed to clone campaign');
            }
        }

        $data = [
            'title' => 'Clone Campaign',
            'campaign' => $campaign
        ];

        $this->view('campaigns/clone', $data);
    }

    public function updateStatus($id)
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $status = $_POST['status'];
            $validStatuses = ['draft', 'active', 'closed', 'draw_done'];

            if (!in_array($status, $validStatuses)) {
                flash('error', 'Invalid status');
                $this->redirect('campaign/show/' . $id);
            }

            if ($this->campaignModel->updateStatus($id, $status)) {
                flash('success', 'Campaign status updated to: ' . $status);
            } else {
                flash('error', 'Failed to update status');
            }
        }

        $this->redirect('campaign/show/' . $id);
    }
    
    public function getProgrammesByStation()
    {
        header('Content-Type: application/json');
        
        $stationId = $_GET['station_id'] ?? null;
        
        if (!$stationId) {
            echo json_encode(['programmes' => [], 'error' => 'No station ID provided']);
            exit;
        }
        
        try {
            $programmes = $this->programmeModel->getByStation($stationId);
            echo json_encode([
                'programmes' => $programmes,
                'count' => count($programmes),
                'station_id' => $stationId
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'programmes' => [],
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function delete($id)
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            flash('error', 'Invalid request method');
            $this->redirect('campaign');
            return;
        }

        verify_csrf();

        $campaign = $this->campaignModel->findById($id);

        if (!$campaign) {
            flash('error', 'Campaign not found');
            $this->redirect('campaign');
            return;
        }

        // Check if user can delete this campaign
        if (!canDelete($campaign, 'campaign')) {
            flash('error', 'You do not have permission to delete this campaign');
            $this->redirect('campaign');
            return;
        }

        // Check if campaign has any tickets
        $ticketModel = $this->model('Ticket');
        $ticketCount = $ticketModel->countByCampaign($id);

        if ($ticketCount > 0) {
            flash('error', 'Cannot delete campaign with existing tickets. This campaign has ' . $ticketCount . ' ticket(s).');
            $this->redirect('campaign');
            return;
        }

        try {
            // Delete campaign programme access first (due to foreign key)
            $this->accessModel->removeAllByCampaign($id);
            
            // Delete the campaign
            if ($this->campaignModel->delete($id)) {
                flash('success', 'Campaign deleted successfully');
            } else {
                flash('error', 'Failed to delete campaign');
            }
        } catch (\Exception $e) {
            flash('error', 'Error deleting campaign: ' . $e->getMessage());
        }

        $this->redirect('campaign');
    }
}
