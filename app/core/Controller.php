<?php

namespace App\Core;

class Controller
{
    protected function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        $modelClass = 'App\\Models\\' . $model;
        return new $modelClass();
    }

    protected function view($view, $data = [])
    {
        extract($data);
        
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist: ' . $view);
        }
    }

    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }

    protected function requireRole($role)
    {
        require_once '../app/core/Middleware.php';
        return \App\Core\Middleware::requireRole($role);
    }

    protected function can($permission)
    {
        require_once '../app/core/Middleware.php';
        return \App\Core\Middleware::can($permission);
    }

    protected function getUser()
    {
        if ($this->isLoggedIn()) {
            return $_SESSION['user'] ?? null;
        }
        return null;
    }
}
