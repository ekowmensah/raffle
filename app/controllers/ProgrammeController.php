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

        $programmes = $this->programmeModel->getAllWithStation();

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

        $data = [
            'title' => 'Programme Details',
            'programme' => $programme
        ];

        $this->view('programmes/view', $data);
    }

    public function create()
    {
        $this->requireAuth();

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

    // AJAX endpoint to get programmes by station
    public function getByStation($stationId)
    {
        $this->requireAuth();

        $programmes = $this->programmeModel->getByStation($stationId);
        $this->json(['success' => true, 'programmes' => $programmes]);
    }
}
