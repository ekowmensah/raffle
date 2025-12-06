<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuditService;

class AuthController extends Controller
{
    private $userModel;
    private $auditService;

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->auditService = new AuditService();
    }

    public function login()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('home');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                flash('error', 'Please fill in all fields');
                $this->redirect('auth/login');
            }

            $user = $this->userModel->authenticate($email, $password);

            if ($user) {
                if (!$user->is_active) {
                    // Log failed login attempt (inactive account)
                    $this->auditService->logLogin($user->id, false);
                    flash('error', 'Your account is inactive. Please contact administrator.');
                    $this->redirect('auth/login');
                }

                $_SESSION['user_id'] = $user->id;
                $_SESSION['user'] = $user;
                $_SESSION['user_id'] = $user->id;

                // Log successful login
                $this->auditService->logLogin($user->id, true);

                flash('success', 'Welcome back, ' . $user->name);
                $this->redirect('home');
            } else {
                // Log failed login attempt
                $this->auditService->log('user_login_failed', 'user', null, ['email' => $email]);
                flash('error', 'Invalid email or password');
                $this->redirect('auth/login');
            }
        }

        $data = ['title' => 'Login'];
        $this->view('auth/login', $data);
    }

    public function logout()
    {
        // Log logout before destroying session
        if (isset($_SESSION['user_id'])) {
            $this->auditService->logLogout($_SESSION['user_id']);
        }
        
        session_unset();
        session_destroy();
        $this->redirect('auth/login');
    }

    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $email = sanitize($_POST['email']);
            
            $userModel = $this->model('User');
            $user = $userModel->findByEmail($email);

            if ($user) {
                $resetModel = $this->model('PasswordReset');
                $token = $resetModel->createToken($email);

                // In production, send email with reset link
                // For now, we'll just show the token
                $resetLink = url('auth/resetPassword?token=' . $token);
                
                flash('success', 'Password reset link: ' . $resetLink . ' (In production, this will be emailed)');
            } else {
                // Don't reveal if email exists
                flash('success', 'If the email exists, a reset link has been sent.');
            }

            $this->redirect('auth/login');
        }

        $data = ['title' => 'Forgot Password'];
        $this->view('auth/forgot-password', $data);
    }

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $token = $_POST['token'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($password !== $confirmPassword) {
                flash('error', 'Passwords do not match');
                $this->redirect('auth/resetPassword?token=' . $token);
            }

            $resetModel = $this->model('PasswordReset');
            $reset = $resetModel->findByToken($token);

            if (!$reset) {
                flash('error', 'Invalid or expired reset token');
                $this->redirect('auth/login');
            }

            // Update password
            $userModel = $this->model('User');
            $user = $userModel->findByEmail($reset->email);

            if ($user) {
                $userModel->update($user->id, [
                    'password' => password_hash($password, PASSWORD_DEFAULT)
                ]);

                // Delete reset token
                $resetModel->deleteByToken($token);

                flash('success', 'Password reset successfully. Please login.');
                $this->redirect('auth/login');
            }
        }

        $resetModel = $this->model('PasswordReset');
        $reset = $resetModel->findByToken($token);

        if (!$reset) {
            flash('error', 'Invalid or expired reset token');
            $this->redirect('auth/login');
        }

        $data = [
            'title' => 'Reset Password',
            'token' => $token
        ];
        $this->view('auth/reset-password', $data);
    }

    public function register()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('home');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'name' => sanitize($_POST['name']),
                'email' => sanitize($_POST['email']),
                'phone' => sanitize($_POST['phone']),
                'password' => $_POST['password'],
                'role_id' => 1, // Default role
                'is_active' => 1
            ];

            // Validation
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                flash('error', 'Please fill in all required fields');
                $this->redirect('auth/register');
            }

            if ($this->userModel->findByEmail($data['email'])) {
                flash('error', 'Email already exists');
                $this->redirect('auth/register');
            }

            $userId = $this->userModel->register($data);

            if ($userId) {
                flash('success', 'Registration successful. Please login.');
                $this->redirect('auth/login');
            } else {
                flash('error', 'Registration failed. Please try again.');
                $this->redirect('auth/register');
            }
        }

        $data = ['title' => 'Register'];
        $this->view('auth/register', $data);
    }
}
