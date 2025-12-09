<?php

namespace App\Controllers;

use App\Core\Controller;

class StationController extends Controller
{
    private $stationModel;

    public function __construct()
    {
        $this->stationModel = $this->model('Station');
    }

    public function index()
    {
        $this->requireAuth();

        // Only super admin can view all stations
        if (!hasRole('super_admin')) {
            flash('error', 'You do not have permission to view stations');
            $this->redirect('home');
        }

        $stations = $this->stationModel->findAll();

        $data = [
            'title' => 'Stations',
            'stations' => $stations
        ];

        $this->view('stations/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $station = $this->stationModel->getWithStats($id);

        if (!$station) {
            flash('error', 'Station not found');
            $this->redirect('station');
        }

        $programmes = $this->stationModel->getProgrammes($id);

        $data = [
            'title' => 'Station Details',
            'station' => $station,
            'programmes' => $programmes
        ];

        $this->view('stations/view', $data);
    }

    public function create()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'code' => sanitize($_POST['code']),
                'short_code_label' => sanitize($_POST['short_code_label']),
                'phone' => sanitize($_POST['phone']),
                'email' => sanitize($_POST['email']),
                'location' => sanitize($_POST['location']),
                'default_station_percent' => $_POST['default_station_percent'] ?? 25,
                'default_programme_percent' => $_POST['default_programme_percent'] ?? 10,
                'default_prize_pool_percent' => $_POST['default_prize_pool_percent'] ?? 40,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->stationModel->create($data)) {
                flash('success', 'Station created successfully');
                $this->redirect('station');
            } else {
                flash('error', 'Failed to create station');
            }
        }

        $data = ['title' => 'Create Station'];
        $this->view('stations/create', $data);
    }

    public function edit($id)
    {
        $this->requireAuth();

        $station = $this->stationModel->findById($id);

        if (!$station) {
            flash('error', 'Station not found');
            $this->redirect('station');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'code' => sanitize($_POST['code']),
                'short_code_label' => sanitize($_POST['short_code_label']),
                'phone' => sanitize($_POST['phone']),
                'email' => sanitize($_POST['email']),
                'location' => sanitize($_POST['location']),
                'default_station_percent' => $_POST['default_station_percent'],
                'default_programme_percent' => $_POST['default_programme_percent'],
                'default_prize_pool_percent' => $_POST['default_prize_pool_percent'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->stationModel->update($id, $data)) {
                flash('success', 'Station updated successfully');
                $this->redirect('station/show/' . $id);
            } else {
                flash('error', 'Failed to update station');
            }
        }

        $data = [
            'title' => 'Edit Station',
            'station' => $station
        ];

        $this->view('stations/edit', $data);
    }

    public function delete($id)
    {
        $this->requireAuth();

        $station = $this->stationModel->findById($id);

        if (!$station) {
            flash('error', 'Station not found');
            $this->redirect('station');
            return;
        }

        // Check if station has any programmes attached
        $programmeModel = $this->model('Programme');
        $programmeCount = $programmeModel->countByStation($id);

        if ($programmeCount > 0) {
            flash('error', 'Cannot delete station with attached programmes. This station has ' . $programmeCount . ' programme(s) attached.');
            $this->redirect('station');
            return;
        }

        // Check if station has any users
        $userModel = $this->model('User');
        $userCount = $userModel->countByStation($id);

        if ($userCount > 0) {
            flash('error', 'Cannot delete station with assigned users. This station has ' . $userCount . ' user(s) assigned.');
            $this->redirect('station');
            return;
        }

        if ($this->stationModel->delete($id)) {
            flash('success', 'Station deleted successfully');
        } else {
            flash('error', 'Failed to delete station');
        }

        $this->redirect('station');
    }
    
    public function toggleStatus($id)
    {
        $this->requireAuth();
        
        if (!hasRole(['super_admin'])) {
            flash('error', 'You do not have permission to change station status');
            $this->redirect('station');
            return;
        }
        
        $station = $this->stationModel->findById($id);
        
        if (!$station) {
            flash('error', 'Station not found');
            $this->redirect('station');
            return;
        }
        
        $newStatus = $station->is_active ? 0 : 1;
        
        if ($this->stationModel->update($id, ['is_active' => $newStatus])) {
            $action = $newStatus ? 'activated' : 'deactivated';
            flash('success', "Station {$action} successfully");
            
            // If deactivating, also deactivate all programmes and campaigns
            if (!$newStatus) {
                $programmeModel = $this->model('Programme');
                $campaignModel = $this->model('Campaign');
                
                $programmeCount = 0;
                $campaignCount = 0;
                
                // Deactivate programmes
                $programmes = $programmeModel->getByStation($id);
                foreach ($programmes as $programme) {
                    if ($programme->is_active) {
                        $programmeModel->update($programme->id, ['is_active' => 0]);
                        $programmeCount++;
                    }
                }
                
                // Deactivate campaigns (both active and paused)
                $campaigns = $campaignModel->getByStation($id);
                foreach ($campaigns as $campaign) {
                    if ($campaign->status === 'active' || $campaign->status === 'paused') {
                        $campaignModel->update($campaign->id, ['status' => 'inactive']);
                        $campaignCount++;
                    }
                }
                
                if ($programmeCount > 0 || $campaignCount > 0) {
                    flash('info', "Deactivated {$programmeCount} programme(s) and {$campaignCount} campaign(s) under this station");
                }
            }
        } else {
            flash('error', 'Failed to update station status');
        }
        
        $this->redirect('station');
    }
}
