<?php

namespace App\Controllers;

use App\Core\Controller;

class UserController extends Controller
{
    private $userModel;
    private $roleModel;
    private $stationModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->roleModel = $this->model('Role');
        $this->stationModel = $this->model('Station');
    }

    public function index()
    {
        $this->requireAuth();

        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        if ($role === 'super_admin' || $role === 'auditor') {
            $users = $this->userModel->getAllWithRoles();
        } elseif ($role === 'station_admin') {
            $users = $this->userModel->getByStation($user->station_id);
        } else {
            flash('error', 'You do not have permission to view users');
            $this->redirect('home');
            return;
        }

        $data = [
            'title' => 'Users',
            'users' => $users
        ];

        $this->view('users/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $user = $this->userModel->findById($id);

        if (!$user) {
            flash('error', 'User not found');
            $this->redirect('user');
        }

        // Get user with role info
        $userWithRole = $this->userModel->getAllWithRoles();
        $userDetails = null;
        foreach ($userWithRole as $u) {
            if ($u->id == $id) {
                $userDetails = $u;
                break;
            }
        }

        $data = [
            'title' => 'User Details',
            'user' => $userDetails ?? $user
        ];

        $this->view('users/view', $data);
    }

    public function create()
    {
        $this->requireAuth();
        
        if (!can('create_station_user') && !hasRole('super_admin')) {
            flash('error', 'You do not have permission to create users');
            $this->redirect('user');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'role_id' => $_POST['role_id'],
                'station_id' => !empty($_POST['station_id']) ? $_POST['station_id'] : null,
                'programme_id' => !empty($_POST['programme_id']) ? $_POST['programme_id'] : null,
                'name' => sanitize($_POST['name']),
                'email' => sanitize($_POST['email']),
                'phone' => sanitize($_POST['phone']),
                'password' => $_POST['password'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Validation
            if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role_id'])) {
                flash('error', 'Please fill in all required fields');
                $_SESSION['old'] = $_POST;
                $this->redirect('user/create');
            }

            // Check if email exists
            if ($this->userModel->findByEmail($data['email'])) {
                flash('error', 'Email already exists');
                $_SESSION['old'] = $_POST;
                $this->redirect('user/create');
            }

            // Check if phone exists
            if (!empty($data['phone']) && $this->userModel->findByPhone($data['phone'])) {
                flash('error', 'Phone number already exists');
                $_SESSION['old'] = $_POST;
                $this->redirect('user/create');
            }

            $userId = $this->userModel->register($data);

            if ($userId) {
                flash('success', 'User created successfully');
                $this->redirect('user');
            } else {
                flash('error', 'Failed to create user');
                $_SESSION['old'] = $_POST;
            }
        }

        $roles = $this->roleModel->findAll();
        $stations = $this->stationModel->getActive();

        $data = [
            'title' => 'Create User',
            'roles' => $roles,
            'stations' => $stations
        ];

        $this->view('users/create', $data);
    }

    public function edit($id)
    {
        $this->requireAuth();
        
        if (!can('edit_station_user') && !hasRole('super_admin')) {
            flash('error', 'You do not have permission to edit users');
            $this->redirect('user');
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            flash('error', 'User not found');
            $this->redirect('user');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'role_id' => $_POST['role_id'],
                'station_id' => !empty($_POST['station_id']) ? $_POST['station_id'] : null,
                'programme_id' => !empty($_POST['programme_id']) ? $_POST['programme_id'] : null,
                'name' => sanitize($_POST['name']),
                'email' => sanitize($_POST['email']),
                'phone' => sanitize($_POST['phone']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Update password only if provided
            if (!empty($_POST['password'])) {
                $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Validation
            if (empty($data['name']) || empty($data['email']) || empty($data['role_id'])) {
                flash('error', 'Please fill in all required fields');
                $this->redirect('user/edit/' . $id);
            }

            // Check if email exists (excluding current user)
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser && $existingUser->id != $id) {
                flash('error', 'Email already exists');
                $this->redirect('user/edit/' . $id);
            }

            // Check if phone exists (excluding current user)
            if (!empty($data['phone'])) {
                $existingUser = $this->userModel->findByPhone($data['phone']);
                if ($existingUser && $existingUser->id != $id) {
                    flash('error', 'Phone number already exists');
                    $this->redirect('user/edit/' . $id);
                }
            }

            if ($this->userModel->update($id, $data)) {
                flash('success', 'User updated successfully');
                $this->redirect('user/show/' . $id);
            } else {
                flash('error', 'Failed to update user');
            }
        }

        $roles = $this->roleModel->findAll();
        $stations = $this->stationModel->getActive();

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'stations' => $stations
        ];

        $this->view('users/edit', $data);
    }

    public function delete($id)
    {
        $this->requireAuth();
        
        // Only super admin can delete users
        if (!hasRole('super_admin')) {
            flash('error', 'Only super administrators can delete users');
            $this->redirect('user');
            return;
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            flash('error', 'User not found');
            $this->redirect('user');
            return;
        }

        // Cannot delete yourself
        if ($id == $_SESSION['user_id']) {
            flash('error', 'You cannot delete your own account');
            $this->redirect('user');
            return;
        }

        if ($this->userModel->delete($id)) {
            flash('success', 'User deleted successfully');
        } else {
            flash('error', 'Failed to delete user');
        }

        $this->redirect('user');
    }

    public function profile()
    {
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'email' => sanitize($_POST['email']),
                'phone' => sanitize($_POST['phone'])
            ];

            // Update password only if provided
            if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
                // Verify current password
                if (!password_verify($_POST['current_password'], $user->password_hash)) {
                    flash('error', 'Current password is incorrect');
                    $this->redirect('user/profile');
                }

                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    flash('error', 'New passwords do not match');
                    $this->redirect('user/profile');
                }

                $data['password_hash'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            }

            if ($this->userModel->update($userId, $data)) {
                // Update session
                $_SESSION['user'] = $this->userModel->findById($userId);
                flash('success', 'Profile updated successfully');
            } else {
                flash('error', 'Failed to update profile');
            }

            $this->redirect('user/profile');
        }

        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];

        $this->view('users/profile', $data);
    }
}
