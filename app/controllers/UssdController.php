<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\UssdSessionService;
use App\Services\UssdMenuService;

class UssdController extends Controller
{
    private $sessionService;
    private $menuService;
    private $playerModel;
    private $ticketModel;
    private $paymentModel;
    
    public function __construct()
    {
        $this->sessionService = new UssdSessionService();
        $this->menuService = new UssdMenuService();
        $this->playerModel = $this->model('Player');
        $this->ticketModel = $this->model('Ticket');
        $this->paymentModel = $this->model('Payment');
    }
    
    /**
     * Main USSD entry point
     */
    public function index()
    {
        // Get raw JSON input from Hubtel
        $rawInput = file_get_contents('php://input');
        error_log('USSD Raw Input: ' . $rawInput);
        
        // Decode JSON request from Hubtel
        $request = json_decode($rawInput, true);
        
        if (!$request) {
            error_log('USSD Error: Invalid JSON received');
            $this->sendHubtelResponse('', 'release', 'Service temporarily unavailable.', 'Error', 'display', 'text');
            return;
        }
        
        // Extract Hubtel parameters
        $sessionId = $request['SessionId'] ?? '';
        $phoneNumber = $request['Mobile'] ?? '';
        $message = $request['Message'] ?? '';
        $type = $request['Type'] ?? '';
        $sequence = $request['Sequence'] ?? 1;
        $clientState = $request['ClientState'] ?? '';
        
        error_log("USSD Request - SessionId: $sessionId, Phone: $phoneNumber, Message: $message, Type: $type, Sequence: $sequence");
        
        // Clean phone number
        $phoneNumber = $this->cleanPhoneNumber($phoneNumber);
        
        // Get or create session
        $session = $this->sessionService->getOrCreateSession($sessionId, $phoneNumber);
        
        // Parse user input from message
        // For Initiation, message is the USSD code (e.g., "*713#")
        // For Response, message is the user's input (e.g., "1", "2", etc.)
        $userInput = '';
        if ($type === 'Response') {
            $userInput = trim($message);
        }
        
        // Route request
        if ($type === 'Initiation' || $sequence == 1) {
            // First request - show main menu
            $menuText = $this->menuService->buildMainMenu();
            $this->sendHubtelResponse($sessionId, 'response', substr($menuText, 4), 'Main Menu', 'input', 'text', $clientState);
        } elseif ($type === 'Timeout') {
            // Session timeout
            $this->sendHubtelResponse($sessionId, 'release', 'Session timed out. Please try again.', 'Timeout', 'display', 'text');
        } else {
            // Handle user response
            $response = $this->routeRequest($session, $userInput, $phoneNumber);
            
            // Parse response to determine type and message
            if (strpos($response, 'END ') === 0) {
                // End session
                $message = substr($response, 4);
                $this->sendHubtelResponse($sessionId, 'release', $message, 'Complete', 'display', 'text');
            } else {
                // Continue session
                $message = substr($response, 4); // Remove "CON " prefix
                $this->sendHubtelResponse($sessionId, 'response', $message, 'Menu', 'input', 'text', $clientState);
            }
        }
    }
    
    /**
     * Send response in Hubtel format
     */
    private function sendHubtelResponse($sessionId, $type, $message, $label, $dataType, $fieldType, $clientState = '')
    {
        $response = [
            'SessionId' => $sessionId,
            'Type' => $type,
            'Message' => $message,
            'Label' => $label
        ];
        
        // Only add DataType and FieldType for interactive messages
        if ($type !== 'release') {
            $response['DataType'] = $dataType;
            $response['FieldType'] = $fieldType;
        }
        
        if (!empty($clientState)) {
            $response['ClientState'] = $clientState;
        }
        
        error_log('USSD Response: ' . json_encode($response));
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    /**
     * Route request based on current step
     */
    private function routeRequest($session, $userInput, $phoneNumber)
    {
        $currentStep = $session->current_step;
        $sessionData = json_decode($session->session_data, true) ?: [];
        
        switch ($currentStep) {
            case 'main_menu':
                return $this->handleMainMenu($session->session_id, $userInput, $phoneNumber);
                
            case 'select_station':
                return $this->handleStationSelection($session->session_id, $userInput, $sessionData);
                
            case 'select_station_campaign':
                return $this->handleStationCampaignSelection($session->session_id, $userInput, $sessionData, $phoneNumber);
                
            case 'select_programme':
                return $this->handleProgrammeSelection($session->session_id, $userInput, $sessionData);
                
            case 'select_campaign':
                return $this->handleCampaignSelection($session->session_id, $userInput, $sessionData, $phoneNumber);
                
            case 'select_quantity':
                return $this->handleQuantitySelection($session->session_id, $userInput, $sessionData);
                
            case 'confirm_payment':
                return $this->handlePaymentConfirmation($session->session_id, $userInput, $sessionData, $phoneNumber);
                
            case 'select_payment_method':
                return $this->handlePaymentMethod($session->session_id, $userInput, $sessionData, $phoneNumber);
                
            case 'enter_payment_number':
                return $this->handlePaymentNumberInput($session->session_id, $userInput, $sessionData, $phoneNumber);
                
            case 'view_tickets':
                return $this->handleTicketNavigation($session->session_id, $userInput, $sessionData, $phoneNumber);
                
            default:
                return $this->menuService->buildMainMenu();
        }
    }
    
    /**
     * Handle main menu selection
     */
    private function handleMainMenu($sessionId, $input, $phoneNumber)
    {
        switch ($input) {
            case '1': // Buy Ticket
                $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => 1]);
                return $this->menuService->buildStationMenu(1);
                
            case '2': // Check My Tickets
                $this->sessionService->updateSession($sessionId, 'view_tickets', ['ticket_page' => 1]);
                return $this->menuService->buildTicketList($phoneNumber, 1);
                
            case '3': // Check Winnings
                $this->sessionService->closeSession($sessionId);
                return $this->menuService->buildWinnerCheck($phoneNumber);
                
            case '4': // My Balance
                $this->sessionService->closeSession($sessionId);
                return $this->menuService->buildBalanceInquiry($phoneNumber);
                
            case '0': // Exit
                $this->sessionService->closeSession($sessionId);
                return "END Thank you for using Raffle System!";
                
            default:
                return $this->menuService->buildMainMenu();
        }
    }
    
    /**
     * Handle station selection with pagination
     */
    private function handleStationSelection($sessionId, $input, $sessionData = [])
    {
        $currentPage = $sessionData['station_page'] ?? 1;
        
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'main_menu');
            return $this->menuService->buildMainMenu();
        }
        
        // Handle pagination
        if ($input == '5') {
            // Next page
            $newPage = $currentPage + 1;
            $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => $newPage]);
            return $this->menuService->buildStationMenu($newPage);
        }
        
        if ($input == '6') {
            // Previous page
            $newPage = max(1, $currentPage - 1);
            $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => $newPage]);
            return $this->menuService->buildStationMenu($newPage);
        }
        
        // Get stations for current page
        $perPage = 4;
        $offset = ($currentPage - 1) * $perPage;
        $stations = $this->menuService->getStationsArray($offset, $perPage);
        $index = (int)$input - 1;
        
        if (!isset($stations[$index])) {
            return "CON Invalid selection. Please try again.\n" . 
                   substr($this->menuService->buildStationMenu($currentPage), 4);
        }
        
        $selectedStation = $stations[$index];
        $this->sessionService->updateSession($sessionId, 'select_station_campaign', [
            'station_id' => $selectedStation->id,
            'station_name' => $selectedStation->name,
            'campaign_page' => 1
        ]);
        
        // Show station-wide campaigns with option to browse by programme
        return $this->menuService->buildStationCampaignMenu($selectedStation->id, 1);
    }
    
    /**
     * Handle station campaign selection (station-wide campaigns or browse by programme)
     */
    private function handleStationCampaignSelection($sessionId, $input, $sessionData, $phoneNumber)
    {
        $currentPage = $sessionData['campaign_page'] ?? 1;
        $stationId = $sessionData['station_id'];
        
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => 1]);
            return $this->menuService->buildStationMenu(1);
        }
        
        // Handle pagination
        if ($input == '5') {
            // Next page
            $newPage = $currentPage + 1;
            $this->sessionService->updateSession($sessionId, 'select_station_campaign', array_merge($sessionData, ['campaign_page' => $newPage]));
            return $this->menuService->buildStationCampaignMenu($stationId, $newPage);
        }
        
        if ($input == '6') {
            // Previous page
            $newPage = max(1, $currentPage - 1);
            $this->sessionService->updateSession($sessionId, 'select_station_campaign', array_merge($sessionData, ['campaign_page' => $newPage]));
            return $this->menuService->buildStationCampaignMenu($stationId, $newPage);
        }
        
        // Check if user selected "Filter by Programme" option (option 7)
        if ($input == '7') {
            // User wants to filter by programme
            $this->sessionService->updateSession($sessionId, 'select_programme', [
                'station_id' => $stationId,
                'station_name' => $sessionData['station_name'],
                'programme_page' => 1
            ]);
            return $this->menuService->buildProgrammeMenu($stationId, 1);
        }
        
        // Get campaigns for current page
        $perPage = 4;
        $offset = ($currentPage - 1) * $perPage;
        $campaigns = $this->menuService->getStationCampaignsArray($stationId, $offset, $perPage);
        $index = (int)$input - 1;
        
        // User selected a campaign
        if (!isset($campaigns[$index])) {
            return "CON Invalid selection. Please try again.\n" . 
                   substr($this->menuService->buildStationCampaignMenu($stationId, $currentPage), 4);
        }
        
        $selectedCampaign = $campaigns[$index];
        $this->sessionService->updateSession($sessionId, 'select_quantity', array_merge($sessionData, [
            'campaign_id' => $selectedCampaign->id,
            'campaign_name' => $selectedCampaign->name,
            'ticket_price' => $selectedCampaign->ticket_price,
            'station_id' => $stationId,
            'programme_id' => null, // Station-wide campaign
            'phone_number' => $phoneNumber
        ]));
        
        return $this->menuService->buildQuantityMenu(
            $selectedCampaign->name,
            $selectedCampaign->ticket_price
        );
    }
    
    /**
     * Handle programme selection with pagination
     */
    private function handleProgrammeSelection($sessionId, $input, $sessionData)
    {
        $currentPage = $sessionData['programme_page'] ?? 1;
        $stationId = $sessionData['station_id'];
        
        if ($input == '0') {
            // Go back to station campaigns
            $this->sessionService->updateSession($sessionId, 'select_station_campaign', [
                'station_id' => $stationId,
                'station_name' => $sessionData['station_name'] ?? ''
            ]);
            return $this->menuService->buildStationCampaignMenu($stationId);
        }
        
        // Handle pagination
        if ($input == '5') {
            // Next page
            $newPage = $currentPage + 1;
            $this->sessionService->updateSession($sessionId, 'select_programme', array_merge($sessionData, ['programme_page' => $newPage]));
            return $this->menuService->buildProgrammeMenu($stationId, $newPage);
        }
        
        if ($input == '6') {
            // Previous page
            $newPage = max(1, $currentPage - 1);
            $this->sessionService->updateSession($sessionId, 'select_programme', array_merge($sessionData, ['programme_page' => $newPage]));
            return $this->menuService->buildProgrammeMenu($stationId, $newPage);
        }
        
        // Get programmes for current page
        $perPage = 4;
        $offset = ($currentPage - 1) * $perPage;
        $programmes = $this->menuService->getProgrammesArray($stationId, $offset, $perPage);
        $index = (int)$input - 1;
        
        if (!isset($programmes[$index])) {
            return "CON Invalid selection. Please try again.\n" . 
                   substr($this->menuService->buildProgrammeMenu($stationId, $currentPage), 4);
        }
        
        $selectedProgramme = $programmes[$index];
        $this->sessionService->updateSession($sessionId, 'select_campaign', array_merge($sessionData, [
            'programme_id' => $selectedProgramme->id,
            'programme_name' => $selectedProgramme->name
        ]));
        
        return $this->menuService->buildCampaignMenu($stationId, $selectedProgramme->id);
    }
    
    /**
     * Handle campaign selection
     */
    private function handleCampaignSelection($sessionId, $input, $sessionData, $phoneNumber)
    {
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_programme', [
                'station_id' => $sessionData['station_id'],
                'station_name' => $sessionData['station_name'] ?? '',
                'programme_page' => 1
            ]);
            return $this->menuService->buildProgrammeMenu($sessionData['station_id'], 1);
        }
        
        $campaigns = $this->menuService->getCampaignsArray(
            $sessionData['station_id'],
            $sessionData['programme_id']
        );
        $index = (int)$input - 1;
        
        if (!isset($campaigns[$index])) {
            return "CON Invalid selection. Please try again.\n" . 
                   substr($this->menuService->buildCampaignMenu(
                       $sessionData['station_id'],
                       $sessionData['programme_id']
                   ), 4);
        }
        
        $selectedCampaign = $campaigns[$index];
        $this->sessionService->updateSession($sessionId, 'select_quantity', array_merge($sessionData, [
            'campaign_id' => $selectedCampaign->id,
            'campaign_name' => $selectedCampaign->name,
            'ticket_price' => $selectedCampaign->ticket_price,
            'phone_number' => $phoneNumber
        ]));
        
        return $this->menuService->buildQuantityMenu(
            $selectedCampaign->name,
            $selectedCampaign->ticket_price
        );
    }
    
    /**
     * Handle quantity selection (direct number input)
     */
    private function handleQuantitySelection($sessionId, $input, $sessionData)
    {
        $quantity = (int)$input;
        $ticketPrice = $sessionData['ticket_price'];
        
        // Validate quantity
        if ($quantity < 1 || $quantity > 1000) {
            return "CON Invalid quantity. Enter 1-1000:\n" .
                   substr($this->menuService->buildQuantityMenu(
                       $sessionData['campaign_name'],
                       $ticketPrice
                   ), 4);
        }
        
        $totalAmount = $quantity * $ticketPrice;
        $this->sessionService->updateSession($sessionId, 'confirm_payment', array_merge($sessionData, [
            'quantity' => $quantity,
            'total_amount' => $totalAmount
        ]));
        
        return $this->menuService->buildPaymentConfirmation(
            $quantity,
            $totalAmount,
            $this->formatPhoneForDisplay($sessionData['phone_number'] ?? '')
        );
    }
    
    /**
     * Handle payment confirmation
     */
    private function handlePaymentConfirmation($sessionId, $input, $sessionData, $phoneNumber)
    {
        if ($input == '0') {
            $this->sessionService->closeSession($sessionId);
            return "END Purchase cancelled.";
        }
        
        if ($input == '1') {
            $this->sessionService->updateSession($sessionId, 'select_payment_method');
            return $this->menuService->buildPaymentMethodMenu($this->formatPhoneForDisplay($phoneNumber));
        }
        
        return "CON Invalid selection.\n" . 
               substr($this->menuService->buildPaymentConfirmation(
                   $sessionData['quantity'],
                   $sessionData['total_amount'],
                   $phoneNumber
               ), 4); // Remove CON prefix to avoid duplication
    }
    
    /**
     * Handle payment method selection and initiate payment
     */
    private function handlePaymentMethod($sessionId, $input, $sessionData, $phoneNumber)
    {
        if ($input == '0') {
            $this->sessionService->closeSession($sessionId);
            return "END Purchase cancelled.";
        }
        
        if ($input == '1') {
            // Use current number
            return $this->processPayment($sessionId, $sessionData, $phoneNumber, $phoneNumber);
        } elseif ($input == '2') {
            // Ask for different number
            $this->sessionService->updateSession($sessionId, 'enter_payment_number');
            return "CON Enter Mobile Money Number:\n(e.g., 0241234567)";
        } else {
            return "CON Invalid selection.\n" . substr($this->menuService->buildPaymentMethodMenu($this->formatPhoneForDisplay($phoneNumber)), 4);
        }
    }
    
    /**
     * Handle payment number input
     */
    private function handlePaymentNumberInput($sessionId, $input, $sessionData, $phoneNumber)
    {
        // Clean and validate the entered number
        $paymentNumber = $this->cleanPhoneNumber($input);
        
        if (empty($paymentNumber) || strlen($paymentNumber) < 10) {
            return "CON Invalid phone number.\nEnter Mobile Money Number:\n(e.g., 0241234567)";
        }
        
        // Process payment with the entered number
        return $this->processPayment($sessionId, $sessionData, $phoneNumber, $paymentNumber);
    }
    
    /**
     * Process payment with Hubtel
     */
    private function processPayment($sessionId, $sessionData, $phoneNumber, $paymentNumber)
    {
        // Validate phone number
        if (empty($phoneNumber)) {
            $this->sessionService->closeSession($sessionId);
            return "END Error: Phone number not available.\nPlease try again.";
        }
        
        // Get or create player
        $player = $this->playerModel->getByPhone($phoneNumber);
        if (!$player) {
            $playerId = $this->playerModel->create([
                'name' => 'USSD User',
                'phone' => $phoneNumber
            ]);
            // Retrieve the newly created player
            $player = $this->playerModel->findById($playerId);
        } else {
            $playerId = $player->id;
        }
        
        // Create payment record
        $reference = 'USSD' . time() . rand(1000, 9999);
        $gateway = 'hubtel';
        
        $paymentData = [
            'player_id' => $playerId,
            'campaign_id' => $sessionData['campaign_id'],
            'station_id' => $sessionData['station_id'],
            'programme_id' => $sessionData['programme_id'] ?? null, // Can be null for station-wide campaigns
            'amount' => $sessionData['total_amount'],
            'gateway' => $gateway,
            'gateway_reference' => $reference,
            'internal_reference' => $reference,
            'status' => 'pending',
            'channel' => 'ussd'
        ];
        
        $paymentId = $this->paymentModel->create($paymentData);
        
        // Initiate mobile money payment via Hubtel
        require_once '../app/services/PaymentGateway/HubtelService.php';
        $hubtelService = new \App\Services\PaymentGateway\HubtelService();
        
        // Get campaign for description
        $campaignModel = $this->model('Campaign');
        $campaign = $campaignModel->findById($sessionData['campaign_id']);
        
        // Initiate Hubtel payment with the payment number (may be different from user's number)
        $hubtelData = [
            'phone' => $paymentNumber,  // Use the payment number (could be different)
            'amount' => $sessionData['total_amount'],
            'reference' => $reference,
            'player_name' => $player->name ?? 'USSD User',
            'description' => 'Raffle Ticket: ' . ($campaign->name ?? 'Campaign'),
            'callback_url' => $this->getCallbackUrl()
        ];
            
            // Debug log - detailed
            error_log('=== USSD Payment Initiation ===');
            error_log('User Phone: ' . $phoneNumber);
            error_log('Payment Phone: ' . $paymentNumber);
            error_log('Amount: ' . $sessionData['total_amount']);
            error_log('Reference: ' . $reference);
            error_log('Campaign: ' . ($campaign->name ?? 'Unknown'));
            error_log('Hubtel Data: ' . json_encode($hubtelData));
            
            $hubtelResponse = $hubtelService->initiatePayment($hubtelData);
            
            // Log response
            error_log('Hubtel Response: ' . json_encode($hubtelResponse));
            
            if ($hubtelResponse['success']) {
                // Update payment with gateway reference
                $this->paymentModel->update($paymentId, [
                    'gateway_reference' => $hubtelResponse['gateway_reference'] ?? $reference,
                    'status' => $hubtelResponse['status'] // 'pending' or 'success'
                ]);
                
                // Close session
                $this->sessionService->closeSession($sessionId);
                
                // Return success message
                return "END Payment initiated successfully!\n\n" .
                       "Amount: ₵" . number_format($sessionData['total_amount'], 2) . "\n" .
                       "Entries: {$sessionData['quantity']}\n\n" .
                       "Please approve the mobile money prompt on your phone.\n\n" .
                       "Or dial *170# and go to Approvals.";
            } else {
                // Payment initiation failed
                $this->paymentModel->update($paymentId, [
                    'status' => 'failed'
                ]);
                
                // Close session
                $this->sessionService->closeSession($sessionId);
                
                return "END Payment failed.\n" .
                       $hubtelResponse['message'] . "\n\n" .
                       "Please try again or contact support.\n" .
                       "Reference: {$reference}";
            }
    }
    
    /**
     * Clean phone number to standard format (233XXXXXXXXX)
     */
    private function cleanPhoneNumber($phone)
    {
        // Remove spaces, dashes, etc.
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Convert to Ghana format (233XXXXXXXXX)
        if (substr($phone, 0, 1) == '0') {
            $phone = '233' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) != '+' && strlen($phone) == 9) {
            $phone = '233' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Format phone number for display (0XXXXXXXXX)
     */
    private function formatPhoneForDisplay($phone)
    {
        // Remove spaces, dashes, etc.
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Convert 233XXXXXXXXX to 0XXXXXXXXX
        if (substr($phone, 0, 3) == '233') {
            return '0' . substr($phone, 3);
        }
        
        // If already starts with 0, return as is
        if (substr($phone, 0, 1) == '0') {
            return $phone;
        }
        
        // If 9 digits, add 0 prefix
        if (strlen($phone) == 9) {
            return '0' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Handle ticket list navigation (next/previous page)
     */
    private function handleTicketNavigation($sessionId, $input, $sessionData, $phoneNumber)
    {
        $currentPage = $sessionData['ticket_page'] ?? 1;
        
        switch ($input) {
            case '1': // Next Page
                $newPage = $currentPage + 1;
                $this->sessionService->updateSession($sessionId, 'view_tickets', ['ticket_page' => $newPage]);
                return $this->menuService->buildTicketList($phoneNumber, $newPage);
                
            case '2': // Previous Page
                $newPage = max(1, $currentPage - 1);
                $this->sessionService->updateSession($sessionId, 'view_tickets', ['ticket_page' => $newPage]);
                return $this->menuService->buildTicketList($phoneNumber, $newPage);
                
            case '0': // Back to Main Menu
                $this->sessionService->updateSession($sessionId, 'main_menu', []);
                return $this->menuService->buildMainMenu();
                
            default:
                return $this->menuService->buildTicketList($phoneNumber, $currentPage);
        }
    }
    
    /**
     * Get callback URL for payment webhooks
     */
    private function getCallbackUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "{$protocol}://{$host}/webhook/hubtel";
    }
}
