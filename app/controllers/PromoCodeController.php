<?php

namespace App\Controllers;

use App\Core\Controller;

class PromoCodeController extends Controller
{
    private $promoModel;

    public function __construct()
    {
        $this->promoModel = $this->model('PromoCode');
    }

    public function index()
    {
        $this->requireAuth();

        $promos = $this->promoModel->findAll();

        $data = [
            'title' => 'Promo Codes',
            'promos' => $promos
        ];

        $this->view('promo-codes/index', $data);
    }

    public function create()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'code' => strtoupper(sanitize($_POST['code'])),
                'name' => sanitize($_POST['name'] ?? ''),
                'extra_commission_percent' => floatval($_POST['extra_commission_percent'] ?? 0),
                'user_id' => !empty($_POST['user_id']) ? $_POST['user_id'] : null,
                'station_id' => $_POST['station_id'], // Required field
                'programme_id' => !empty($_POST['programme_id']) ? $_POST['programme_id'] : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Check if code exists
            if ($this->promoModel->findByCode($data['code'])) {
                flash('error', 'Promo code already exists');
                $_SESSION['old'] = $_POST;
                $this->redirect('promocode/create');
            }

            if ($this->promoModel->create($data)) {
                flash('success', 'Promo code created successfully');
                $this->redirect('promocode');
            } else {
                flash('error', 'Failed to create promo code');
            }
        }

        $userModel = $this->model('User');
        $stationModel = $this->model('Station');
        $programmeModel = $this->model('Programme');

        $data = [
            'title' => 'Create Promo Code',
            'users' => $userModel->findAll(),
            'stations' => $stationModel->findAll(),
            'programmes' => $programmeModel->findAll()
        ];

        $this->view('promo-codes/create', $data);
    }

    public function edit($id)
    {
        $this->requireAuth();

        $promo = $this->promoModel->findById($id);

        if (!$promo) {
            flash('error', 'Promo code not found');
            $this->redirect('promocode');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'extra_commission_percent' => floatval($_POST['extra_commission_percent'] ?? 0),
                'user_id' => !empty($_POST['user_id']) ? $_POST['user_id'] : null,
                'station_id' => $_POST['station_id'], // Required field
                'programme_id' => !empty($_POST['programme_id']) ? $_POST['programme_id'] : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->promoModel->update($id, $data)) {
                flash('success', 'Promo code updated successfully');
                $this->redirect('promocode');
            } else {
                flash('error', 'Failed to update promo code');
            }
        }

        $userModel = $this->model('User');
        $stationModel = $this->model('Station');
        $programmeModel = $this->model('Programme');

        $data = [
            'title' => 'Edit Promo Code',
            'promo' => $promo,
            'users' => $userModel->findAll(),
            'stations' => $stationModel->findAll(),
            'programmes' => $programmeModel->findAll()
        ];

        $this->view('promo-codes/edit', $data);
    }

    public function analytics($id)
    {
        $this->requireAuth();

        $promo = $this->promoModel->findById($id);

        if (!$promo) {
            flash('error', 'Promo code not found');
            $this->redirect('promocode');
        }

        $stats = $this->promoModel->getUsageStats($id);

        $data = [
            'title' => 'Promo Code Analytics',
            'promo' => $promo,
            'stats' => $stats
        ];

        $this->view('promo-codes/analytics', $data);
    }

    public function delete($id)
    {
        $this->requireAuth();

        if ($this->promoModel->delete($id)) {
            flash('success', 'Promo code deleted successfully');
        } else {
            flash('error', 'Failed to delete promo code');
        }

        $this->redirect('promocode');
    }
}
