<?php

namespace App\Controllers\Api;

class AuthController extends ApiController
{
    private $playerModel;
    
    public function __construct()
    {
        parent::__construct();
        require_once '../app/models/Player.php';
        $this->playerModel = new \App\Models\Player();
    }
    
    /**
     * Send OTP for registration/login
     * POST /api/auth/send-otp
     */
    public function sendOtp()
    {
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'phone' => 'required|min:10|max:15'
        ]);
        
        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }
        
        $phoneNumber = $this->cleanPhoneNumber($input['phone']);
        
        // Send OTP
        $sent = $this->authService->sendOtp($phoneNumber);
        
        if ($sent) {
            $this->success([], 'OTP sent successfully');
        } else {
            $this->error('Failed to send OTP', 500);
        }
    }
    
    /**
     * Verify OTP and login/register
     * POST /api/auth/verify-otp
     */
    public function verifyOtp()
    {
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'phone' => 'required',
            'otp' => 'required|min:6|max:6'
        ]);
        
        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }
        
        $phoneNumber = $this->cleanPhoneNumber($input['phone']);
        $otp = $input['otp'];
        
        // Verify OTP
        $verified = $this->authService->verifyOtp($phoneNumber, $otp);
        
        if (!$verified) {
            $this->error('Invalid or expired OTP', 400);
        }
        
        // Get or create player
        $player = $this->playerModel->getByPhone($phoneNumber);
        
        if (!$player) {
            // Register new player
            $playerId = $this->playerModel->create([
                'name' => $input['name'] ?? 'Player',
                'phone' => $phoneNumber,
                'email' => $input['email'] ?? null
            ]);
            
            $player = $this->playerModel->find($playerId);
        }
        
        // Generate token
        $token = $this->authService->generateToken($player->id, $player->phone);
        
        $this->success([
            'token' => $token,
            'player' => [
                'id' => $player->id,
                'name' => $player->name,
                'phone' => $player->phone,
                'email' => $player->email,
                'loyalty_points' => $player->loyalty_points
            ]
        ], 'Login successful');
    }
    
    /**
     * Get current player profile
     * GET /api/auth/me
     */
    public function me()
    {
        $player = $this->requireAuth();
        
        $this->success([
            'id' => $player->id,
            'name' => $player->name,
            'phone' => $player->phone,
            'email' => $player->email,
            'loyalty_points' => $player->loyalty_points,
            'created_at' => $player->created_at
        ]);
    }
    
    /**
     * Update player profile
     * PUT /api/auth/profile
     */
    public function updateProfile()
    {
        $player = $this->requireAuth();
        $input = $this->getJsonInput();
        
        $updateData = [];
        
        if (isset($input['name'])) {
            $updateData['name'] = $input['name'];
        }
        
        if (isset($input['email'])) {
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $this->error('Invalid email format', 422);
            }
            $updateData['email'] = $input['email'];
        }
        
        if (!empty($updateData)) {
            $this->playerModel->update($player->id, $updateData);
        }
        
        $updatedPlayer = $this->playerModel->find($player->id);
        
        $this->success([
            'id' => $updatedPlayer->id,
            'name' => $updatedPlayer->name,
            'phone' => $updatedPlayer->phone,
            'email' => $updatedPlayer->email,
            'loyalty_points' => $updatedPlayer->loyalty_points
        ], 'Profile updated successfully');
    }
    
    /**
     * Clean phone number
     */
    private function cleanPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (substr($phone, 0, 1) == '0') {
            $phone = '233' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) != '+' && strlen($phone) == 9) {
            $phone = '233' . $phone;
        }
        
        return $phone;
    }
}
