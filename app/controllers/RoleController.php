<?php

namespace App\Controllers;

use App\Core\Controller;

class RoleController extends Controller
{
    private $roleModel;

    public function __construct()
    {
        $this->roleModel = $this->model('Role');
    }

    public function index()
    {
        $this->requireAuth();

        $roles = $this->roleModel->getAllWithUserCount();

        $data = [
            'title' => 'Roles',
            'roles' => $roles
        ];

        $this->view('roles/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $role = $this->roleModel->findById($id);

        if (!$role) {
            flash('error', 'Role not found');
            $this->redirect('role');
        }

        $userCount = $this->roleModel->getUserCount($id);

        $data = [
            'title' => 'Role Details',
            'role' => $role,
            'user_count' => $userCount
        ];

        $this->view('roles/view', $data);
    }

    public function create()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description'])
            ];

            // Validation
            if (empty($data['name'])) {
                flash('error', 'Role name is required');
                $this->redirect('role/create');
            }

            // Check if role name already exists
            if ($this->roleModel->findByName($data['name'])) {
                flash('error', 'Role name already exists');
                $this->redirect('role/create');
            }

            if ($this->roleModel->create($data)) {
                flash('success', 'Role created successfully');
                $this->redirect('role');
            } else {
                flash('error', 'Failed to create role');
            }
        }

        $data = ['title' => 'Create Role'];
        $this->view('roles/create', $data);
    }

    public function edit($id)
    {
        $this->requireAuth();

        $role = $this->roleModel->findById($id);

        if (!$role) {
            flash('error', 'Role not found');
            $this->redirect('role');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description'])
            ];

            // Validation
            if (empty($data['name'])) {
                flash('error', 'Role name is required');
                $this->redirect('role/edit/' . $id);
            }

            // Check if role name already exists (excluding current role)
            $existingRole = $this->roleModel->findByName($data['name']);
            if ($existingRole && $existingRole->id != $id) {
                flash('error', 'Role name already exists');
                $this->redirect('role/edit/' . $id);
            }

            if ($this->roleModel->update($id, $data)) {
                flash('success', 'Role updated successfully');
                $this->redirect('role/show/' . $id);
            } else {
                flash('error', 'Failed to update role');
            }
        }

        $data = [
            'title' => 'Edit Role',
            'role' => $role
        ];

        $this->view('roles/edit', $data);
    }

    public function delete($id)
    {
        $this->requireAuth();

        $role = $this->roleModel->findById($id);

        if (!$role) {
            flash('error', 'Role not found');
            $this->redirect('role');
        }

        // Check if role can be deleted (no users assigned)
        if (!$this->roleModel->canDelete($id)) {
            flash('error', 'Cannot delete role with assigned users');
            $this->redirect('role');
        }

        if ($this->roleModel->delete($id)) {
            flash('success', 'Role deleted successfully');
        } else {
            flash('error', 'Failed to delete role');
        }

        $this->redirect('role');
    }
}
