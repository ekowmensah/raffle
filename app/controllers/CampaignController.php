<?php

namespace App\Controllers;

use App\Core\Controller;

class CampaignController extends Controller
{
    private $campaignModel;
    private $sponsorModel;
    private $programmeModel;
    private $accessModel;
    private $ticketModel;

    public function __construct()
    {
        $this->campaignModel = $this->model('Campaign');
        $this->sponsorModel = $this->model('Sponsor');
        $this->programmeModel = $this->model('Programme');
        $this->accessModel = $this->model('CampaignProgrammeAccess');
        $this->ticketModel = $this->model('Ticket');
    }

    public function index()
    {
        $this->requireAuth();

        // Get campaigns based on user role
        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        // Get status filter from query parameter
        $statusFilter = $_GET['status'] ?? 'all';
        
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
        
        // Filter by status if not 'all'
        if ($statusFilter !== 'all') {
            $campaigns = array_filter($campaigns, function($campaign) use ($statusFilter) {
                return $campaign->status === $statusFilter;
            });
        }
        
        // Count campaigns by status
        $allCampaigns = $role === 'super_admin' || $role === 'auditor' 
            ? $this->campaignModel->getAllWithDetails()
            : ($role === 'station_admin' 
                ? $this->campaignModel->getByStation($user->station_id)
                : $this->campaignModel->getByProgramme($user->programme_id));
        
        $statusCounts = [
            'all' => count($allCampaigns),
            'active' => 0,
            'paused' => 0,
            'inactive' => 0,
            'draft' => 0,
            'closed' => 0,
            'draw_done' => 0
        ];
        
        foreach ($allCampaigns as $camp) {
            if (isset($statusCounts[$camp->status])) {
                $statusCounts[$camp->status]++;
            }
        }

        $data = [
            'title' => 'Campaigns',
            'campaigns' => $campaigns,
            'statusFilter' => $statusFilter,
            'statusCounts' => $statusCounts
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
                'platform_percent' => $_POST['platform_percent'] ?? 30,
                'station_percent' => $_POST['station_percent'] ?? 20,
                'programme_percent' => $_POST['programme_percent'] ?? 0,
                'prize_pool_percent' => $_POST['prize_pool_percent'] ?? 50,
                'daily_share_percent_of_pool' => $_POST['daily_share_percent_of_pool'] ?? 50,
                'final_share_percent_of_pool' => $_POST['final_share_percent_of_pool'] ?? 50,
                'daily_draw_enabled' => isset($_POST['daily_draw_enabled']) ? 1 : 0,
                'created_by_user_id' => $_SESSION['user_id'],
                // Item campaign fields
                'campaign_type' => $_POST['campaign_type'] ?? 'cash',
                'item_name' => !empty($_POST['item_name']) ? sanitize($_POST['item_name']) : null,
                'item_description' => !empty($_POST['item_description']) ? sanitize($_POST['item_description']) : null,
                'item_value' => !empty($_POST['item_value']) ? $_POST['item_value'] : null,
                'item_image' => !empty($_POST['item_image']) ? sanitize($_POST['item_image']) : null,
                'item_quantity' => !empty($_POST['item_quantity']) ? $_POST['item_quantity'] : 1,
                'winner_selection_type' => $_POST['winner_selection_type'] ?? 'single',
                'min_tickets_for_draw' => !empty($_POST['min_tickets_for_draw']) ? $_POST['min_tickets_for_draw'] : null
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
        
        // Get station name for station admin
        $user = $_SESSION['user'];
        if (hasRole('station_admin') && $user->station_id) {
            $station = $stationModel->findById($user->station_id);
            $_SESSION['user']->station_name = $station->name ?? '';
        }

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
            $this->redirect('campaign/show/' . $id);
        }

        // Check if campaign has tickets sold - fetch count explicitly
        $ticketCount = $this->ticketModel->countByCampaign($id);
        if ($ticketCount > 0) {
            flash('error', 'Cannot edit campaign - ' . $ticketCount . ' ticket(s) have already been sold. Create a new campaign instead.');
            $this->redirect('campaign/show/' . $id);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            // Handle image upload
            $itemImagePath = $_POST['item_image'] ?? null; // Keep existing image by default
            
            if (isset($_FILES['item_image_upload']) && $_FILES['item_image_upload']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['item_image_upload'];
                
                // Validate file
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!in_array($file['type'], $allowedTypes)) {
                    flash('error', 'Invalid image type. Only JPG, PNG, GIF, and WEBP are allowed.');
                    $this->redirect('campaign/edit/' . $id);
                    return;
                }
                
                if ($file['size'] > $maxSize) {
                    flash('error', 'Image size must be less than 5MB.');
                    $this->redirect('campaign/edit/' . $id);
                    return;
                }
                
                // Create upload directory if it doesn't exist
                $uploadDir = 'uploads/campaigns/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'campaign_' . $id . '_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                // Delete old image if exists
                if (!empty($campaign->item_image) && file_exists($campaign->item_image)) {
                    unlink($campaign->item_image);
                }
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $itemImagePath = $uploadPath;
                } else {
                    flash('error', 'Failed to upload image.');
                    $this->redirect('campaign/edit/' . $id);
                    return;
                }
            }

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
                'daily_draw_enabled' => isset($_POST['daily_draw_enabled']) ? 1 : 0,
                // Item campaign fields
                'campaign_type' => $_POST['campaign_type'] ?? 'cash',
                'item_name' => !empty($_POST['item_name']) ? sanitize($_POST['item_name']) : null,
                'item_description' => !empty($_POST['item_description']) ? sanitize($_POST['item_description']) : null,
                'item_value' => !empty($_POST['item_value']) ? $_POST['item_value'] : null,
                'item_image' => $itemImagePath,
                'item_quantity' => !empty($_POST['item_quantity']) ? $_POST['item_quantity'] : 1,
                'winner_selection_type' => $_POST['winner_selection_type'] ?? 'single',
                'min_tickets_for_draw' => !empty($_POST['min_tickets_for_draw']) ? $_POST['min_tickets_for_draw'] : null
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
            $validStatuses = ['draft', 'active', 'paused', 'inactive', 'closed', 'draw_done'];

            if (!in_array($status, $validStatuses)) {
                flash('error', 'Invalid status');
                $this->redirect('campaign/show/' . $id);
                return;
            }
            
            $campaign = $this->campaignModel->findById($id);
            
            if (!$campaign) {
                flash('error', 'Campaign not found');
                $this->redirect('campaign');
                return;
            }
            
            // Validate status transitions
            if ($status === 'paused' && $campaign->status !== 'active') {
                flash('error', 'Only active campaigns can be paused. Use the Pause button instead.');
                $this->redirect('campaign/show/' . $id);
                return;
            }
            
            if ($status === 'active' && ($campaign->status === 'paused' || $campaign->status === 'inactive')) {
                flash('error', 'Use the Resume/Reactivate button to activate paused or inactive campaigns.');
                $this->redirect('campaign/show/' . $id);
                return;
            }
            
            // Prevent manual setting of inactive status
            if ($status === 'inactive') {
                flash('error', 'Inactive status is set automatically when station/programme is deactivated.');
                $this->redirect('campaign/show/' . $id);
                return;
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
    
    public function pause($id)
    {
        $this->requireAuth();
        
        $campaign = $this->campaignModel->findById($id);
        
        if (!$campaign) {
            flash('error', 'Campaign not found');
            $this->redirect('campaign');
            return;
        }
        
        if (!canEdit($campaign, 'campaign')) {
            flash('error', 'You do not have permission to pause this campaign');
            $this->redirect('campaign');
            return;
        }
        
        if ($campaign->status !== 'active') {
            flash('error', 'Only active campaigns can be paused');
            $this->redirect('campaign');
            return;
        }
        
        if ($this->campaignModel->update($id, ['status' => 'paused'])) {
            flash('success', 'Campaign paused successfully. No new tickets can be purchased.');
        } else {
            flash('error', 'Failed to pause campaign');
        }
        
        $this->redirect('campaign');
    }
    
    public function resume($id)
    {
        $this->requireAuth();
        
        $campaign = $this->campaignModel->findById($id);
        
        if (!$campaign) {
            flash('error', 'Campaign not found');
            $this->redirect('campaign');
            return;
        }
        
        if (!canEdit($campaign, 'campaign')) {
            flash('error', 'You do not have permission to resume this campaign');
            $this->redirect('campaign');
            return;
        }
        
        if ($campaign->status !== 'paused' && $campaign->status !== 'inactive') {
            flash('error', 'Only paused or inactive campaigns can be resumed/reactivated');
            $this->redirect('campaign');
            return;
        }
        
        // Check if station and programme are still active
        $stationModel = $this->model('Station');
        $station = $stationModel->findById($campaign->station_id);
        
        if (!$station || !$station->is_active) {
            flash('error', 'Cannot resume campaign. The station is inactive.');
            $this->redirect('campaign');
            return;
        }
        
        // Check if any associated programme is active
        $programmes = $this->accessModel->getProgrammesByCampaign($id);
        $hasActiveProgramme = false;
        
        foreach ($programmes as $programme) {
            if ($programme->is_active) {
                $hasActiveProgramme = true;
                break;
            }
        }
        
        if (!$hasActiveProgramme && count($programmes) > 0) {
            flash('error', 'Cannot resume campaign. All associated programmes are inactive.');
            $this->redirect('campaign');
            return;
        }
        
        if ($this->campaignModel->update($id, ['status' => 'active'])) {
            $action = $campaign->status === 'inactive' ? 'reactivated' : 'resumed';
            flash('success', "Campaign {$action} successfully. Tickets can now be purchased.");
        } else {
            flash('error', 'Failed to resume campaign');
        }
        
        $this->redirect('campaign');
    }
}
