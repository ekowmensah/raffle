<?php

namespace App\Controllers\Api;

use App\Services\ApiAuthService;

class ApiController
{
    protected $authService;
    protected $player;
    
    public function __construct()
    {
        $this->authService = new ApiAuthService();
        
        // Set JSON header
        header('Content-Type: application/json');
        
        // Handle CORS
        $this->handleCors();
    }
    
    /**
     * Handle CORS headers
     */
    protected function handleCors()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            }
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            
            exit(0);
        }
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        $token = $this->getBearerToken();
        
        if (!$token) {
            $this->error('Authentication required', 401);
        }
        
        $this->player = $this->authService->getPlayerFromToken($token);
        
        if (!$this->player) {
            $this->error('Invalid or expired token', 401);
        }
        
        return $this->player;
    }
    
    /**
     * Get bearer token from header
     */
    protected function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
    
    /**
     * Get authorization header
     */
    protected function getAuthorizationHeader()
    {
        $headers = null;
        
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }
    
    /**
     * Get request body as JSON
     */
    protected function getJsonInput()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
    
    /**
     * Send success response
     */
    protected function success($data = [], $message = 'Success', $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
    
    /**
     * Send error response
     */
    protected function error($message = 'Error', $statusCode = 400, $errors = [])
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]);
        exit;
    }
    
    /**
     * Validate required fields
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $r) {
                if ($r == 'required' && empty($data[$field])) {
                    $errors[$field] = ucfirst($field) . ' is required';
                }
                
                if (strpos($r, 'min:') === 0) {
                    $min = (int)substr($r, 4);
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least {$min} characters";
                    }
                }
                
                if (strpos($r, 'max:') === 0) {
                    $max = (int)substr($r, 4);
                    if (isset($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[$field] = ucfirst($field) . " must not exceed {$max} characters";
                    }
                }
                
                if ($r == 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email';
                }
                
                if ($r == 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field] = ucfirst($field) . ' must be numeric';
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Paginate results
     */
    protected function paginate($query, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $countQuery = preg_replace('/SELECT .* FROM/i', 'SELECT COUNT(*) as total FROM', $query);
        // Remove ORDER BY for count
        $countQuery = preg_replace('/ORDER BY .*/i', '', $countQuery);
        
        return [
            'query' => $query . " LIMIT {$offset}, {$perPage}",
            'page' => $page,
            'per_page' => $perPage
        ];
    }
}
