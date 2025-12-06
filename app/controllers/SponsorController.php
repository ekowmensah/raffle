<?php

namespace App\Controllers;

use App\Core\Controller;

class SponsorController extends Controller
{
    private $sponsorModel;

    public function __construct()
    {
        $this->sponsorModel = $this->model('Sponsor');
    }

    public function index()
    {
        $this->requireAuth();

        $sponsors = $this->sponsorModel->getAllWithStats();

        $data = [
            'title' => 'Sponsors',
            'sponsors' => $sponsors
        ];

        $this->view('sponsors/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $sponsor = $this->sponsorModel->findById($id);

        if (!$sponsor) {
            flash('error', 'Sponsor not found');
            $this->redirect('sponsor');
        }

        $campaignCount = $this->sponsorModel->getCampaignCount($id);

        $data = [
            'title' => 'Sponsor Details',
            'sponsor' => $sponsor,
            'campaign_count' => $campaignCount
        ];

        $this->view('sponsors/view', $data);
    }

    public function create()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'contact_person' => sanitize($_POST['contact_person']),
                'phone' => sanitize($_POST['phone']),
                'email' => sanitize($_POST['email']),
                'notes' => sanitize($_POST['notes'] ?? '')
            ];

            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../public/assets/uploads/sponsors/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExt = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExt, $allowedExts)) {
                    $fileName = uniqid('sponsor_') . '.' . $fileExt;
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $filePath)) {
                        $data['logo_url'] = 'assets/uploads/sponsors/' . $fileName;
                    }
                }
            }

            // Validation
            if (empty($data['name'])) {
                flash('error', 'Sponsor name is required');
                $_SESSION['old'] = $_POST;
                $this->redirect('sponsor/create');
            }

            if ($this->sponsorModel->create($data)) {
                flash('success', 'Sponsor created successfully');
                $this->redirect('sponsor');
            } else {
                flash('error', 'Failed to create sponsor');
                $_SESSION['old'] = $_POST;
            }
        }

        $data = ['title' => 'Create Sponsor'];
        $this->view('sponsors/create', $data);
    }

    public function edit($id)
    {
        $this->requireAuth();

        $sponsor = $this->sponsorModel->findById($id);

        if (!$sponsor) {
            flash('error', 'Sponsor not found');
            $this->redirect('sponsor');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'contact_person' => sanitize($_POST['contact_person']),
                'phone' => sanitize($_POST['phone']),
                'email' => sanitize($_POST['email']),
                'notes' => sanitize($_POST['notes'] ?? '')
            ];

            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../public/assets/uploads/sponsors/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExt = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExt, $allowedExts)) {
                    // Delete old logo if exists
                    if (!empty($sponsor->logo_path) && file_exists('../public/' . $sponsor->logo_path)) {
                        unlink('../public/' . $sponsor->logo_path);
                    }

                    $fileName = uniqid('sponsor_') . '.' . $fileExt;
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $filePath)) {
                        $data['logo_path'] = 'assets/uploads/sponsors/' . $fileName;
                    }
                }
            }

            if ($this->sponsorModel->update($id, $data)) {
                flash('success', 'Sponsor updated successfully');
                $this->redirect('sponsor/show/' . $id);
            } else {
                flash('error', 'Failed to update sponsor');
            }
        }

        $data = [
            'title' => 'Edit Sponsor',
            'sponsor' => $sponsor
        ];

        $this->view('sponsors/edit', $data);
    }

    public function delete($id)
    {
        $this->requireAuth();

        $sponsor = $this->sponsorModel->findById($id);

        if (!$sponsor) {
            flash('error', 'Sponsor not found');
            $this->redirect('sponsor');
        }

        // Check if sponsor has campaigns
        $campaignCount = $this->sponsorModel->getCampaignCount($id);
        if ($campaignCount > 0) {
            flash('error', 'Cannot delete sponsor with associated campaigns');
            $this->redirect('sponsor');
        }

        // Delete logo file if exists
        if (!empty($sponsor->logo_url) && file_exists('../public/' . $sponsor->logo_url)) {
            unlink('../public/' . $sponsor->logo_url);
        }

        if ($this->sponsorModel->delete($id)) {
            flash('success', 'Sponsor deleted successfully');
        } else {
            flash('error', 'Failed to delete sponsor');
        }

        $this->redirect('sponsor');
    }
}
