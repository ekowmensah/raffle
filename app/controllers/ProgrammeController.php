<?php

namespace App\Controllers;

use App\Core\Controller;

class ProgrammeController extends Controller
{
    private $programmeModel;
    private $stationModel;

    public function __construct()
    {
        $this->programmeModel = $this->model('Programme');
        $this->stationModel = $this->model('Station');
    }

    public function index()
    {
        $this->requireAuth();

        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        if ($role === 'super_admin' || $role === 'auditor') {
            $programmes = $this->programmeModel->getAllWithStation();
        } elseif ($role === 'station_admin') {
            $programmes = $this->programmeModel->getByStation($user->station_id, false); // Show all, including inactive
        } elseif ($role === 'programme_manager') {
            $programme = $this->programmeModel->findById($user->programme_id);
            $programmes = $programme ? [$programme] : [];
        } else {
            $programmes = [];
        }

        $data = [
            'title' => 'Programmes',
            'programmes' => $programmes
        ];

        $this->view('programmes/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $programme = $this->programmeModel->getWithStats($id);

        if (!$programme) {
            flash('error', 'Programme not found');
            $this->redirect('programme');
        }

        if (!canAccessProgramme($programme)) {
            flash('error', 'You do not have permission to view this programme');
            $this->redirect('programme');
        }

        $data = [
            'title' => 'Programme Details',
            'programme' => $programme
        ];

        $this->view('programmes/view', $data);
    }

    public function create()
    {
        $this->requireAuth();
        
        if (!can('create_programme')) {
            flash('error', 'You do not have permission to create programmes');
            $this->redirect('programme');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'station_id' => $_POST['station_id'],
                'name' => sanitize($_POST['name']),
                'code' => sanitize($_POST['code']),
                'ussd_option_number' => !empty($_POST['ussd_option_number']) ? $_POST['ussd_option_number'] : null,
                'station_percent' => !empty($_POST['station_percent']) ? $_POST['station_percent'] : null,
                'programme_percent' => !empty($_POST['programme_percent']) ? $_POST['programme_percent'] : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Validation
            if (empty($data['station_id']) || empty($data['name']) || empty($data['code'])) {
                flash('error', 'Please fill in all required fields');
                $_SESSION['old'] = $_POST;
                $this->redirect('programme/create');
            }

            // Check if code exists for this station
            if ($this->programmeModel->findByCode($data['station_id'], $data['code'])) {
                flash('error', 'Programme code already exists for this station');
                $_SESSION['old'] = $_POST;
                $this->redirect('programme/create');
            }

            if ($this->programmeModel->create($data)) {
                flash('success', 'Programme created successfully');
                $this->redirect('programme');
            } else {
                flash('error', 'Failed to create programme');
                $_SESSION['old'] = $_POST;
            }
        }

        $stations = $this->stationModel->getActive();
        
        // Get station name for station admin
        $user = $_SESSION['user'];
        if (hasRole('station_admin') && $user->station_id) {
            $station = $this->stationModel->findById($user->station_id);
            $_SESSION['user']->station_name = $station->name ?? '';
        }

        $data = [
            'title' => 'Create Programme',
            'stations' => $stations
        ];

        $this->view('programmes/create', $data);
    }

    public function edit($id)
    {
        $this->requireAuth();

        $programme = $this->programmeModel->findById($id);

        if (!$programme) {
            flash('error', 'Programme not found');
            $this->redirect('programme');
        }

        if (!canEdit($programme, 'programme')) {
            flash('error', 'You do not have permission to edit this programme');
            $this->redirect('programme');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'station_id' => $_POST['station_id'],
                'name' => sanitize($_POST['name']),
                'code' => sanitize($_POST['code']),
                'ussd_option_number' => !empty($_POST['ussd_option_number']) ? $_POST['ussd_option_number'] : null,
                'station_percent' => !empty($_POST['station_percent']) ? $_POST['station_percent'] : null,
                'programme_percent' => !empty($_POST['programme_percent']) ? $_POST['programme_percent'] : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Validation
            if (empty($data['station_id']) || empty($data['name']) || empty($data['code'])) {
                flash('error', 'Please fill in all required fields');
                $this->redirect('programme/edit/' . $id);
            }

            // Check if code exists for this station (excluding current programme)
            $existing = $this->programmeModel->findByCode($data['station_id'], $data['code']);
            if ($existing && $existing->id != $id) {
                flash('error', 'Programme code already exists for this station');
                $this->redirect('programme/edit/' . $id);
            }

            if ($this->programmeModel->update($id, $data)) {
                flash('success', 'Programme updated successfully');
                $this->redirect('programme/show/' . $id);
            } else {
                flash('error', 'Failed to update programme');
            }
        }

        $stations = $this->stationModel->getActive();

        $data = [
            'title' => 'Edit Programme',
            'programme' => $programme,
            'stations' => $stations
        ];

        $this->view('programmes/edit', $data);
    }

    public function delete($id)
    {
        $this->requireAuth();

        $programme = $this->programmeModel->findById($id);

        if (!$programme) {
            flash('error', 'Programme not found');
            $this->redirect('programme');
            return;
        }

        if (!canDelete($programme, 'programme')) {
            flash('error', 'You do not have permission to delete this programme');
            $this->redirect('programme');
            return;
        }

        // Check if programme has any campaigns attached
        $accessModel = $this->model('CampaignProgrammeAccess');
        $campaignCount = $accessModel->countByProgramme($id);

        if ($campaignCount > 0) {
            flash('error', 'Cannot delete programme with attached campaigns. This programme has ' . $campaignCount . ' campaign(s) attached.');
            $this->redirect('programme');
            return;
        }

        // Check if programme has any users
        $userModel = $this->model('User');
        $userCount = $userModel->countByProgramme($id);

        if ($userCount > 0) {
            flash('error', 'Cannot delete programme with assigned users. This programme has ' . $userCount . ' user(s) assigned.');
            $this->redirect('programme');
            return;
        }

        if ($this->programmeModel->delete($id)) {
            flash('success', 'Programme deleted successfully');
        } else {
            flash('error', 'Failed to delete programme');
        }

        $this->redirect('programme');
    }
    
    public function toggleStatus($id)
    {
        $this->requireAuth();
        
        $programme = $this->programmeModel->findById($id);
        
        if (!$programme) {
            flash('error', 'Programme not found');
            $this->redirect('programme');
            return;
        }
        
        if (!canEdit($programme, 'programme')) {
            flash('error', 'You do not have permission to change programme status');
            $this->redirect('programme');
            return;
        }
        
        $newStatus = $programme->is_active ? 0 : 1;
        
        if ($this->programmeModel->update($id, ['is_active' => $newStatus])) {
            $action = $newStatus ? 'activated' : 'deactivated';
            flash('success', "Programme {$action} successfully");
            
            // If deactivating, also deactivate all campaigns under this programme
            if (!$newStatus) {
                $campaignModel = $this->model('Campaign');
                $campaigns = $campaignModel->getByProgramme($id);
                
                $campaignCount = 0;
                foreach ($campaigns as $campaign) {
                    if ($campaign->status === 'active' || $campaign->status === 'paused') {
                        $campaignModel->update($campaign->id, ['status' => 'inactive']);
                        $campaignCount++;
                    }
                }
                
                if ($campaignCount > 0) {
                    flash('info', "Deactivated {$campaignCount} campaign(s) under this programme");
                }
            }
        } else {
            flash('error', 'Failed to update programme status');
        }
        
        $this->redirect('programme');
    }

    // AJAX endpoint to get programmes by station
    public function getByStation($stationId)
    {
        $this->requireAuth();

        $programmes = $this->programmeModel->getByStation($stationId);
        $this->json(['success' => true, 'programmes' => $programmes]);
    }
}
