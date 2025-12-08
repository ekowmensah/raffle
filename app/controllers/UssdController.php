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
        // Get USSD parameters (format depends on gateway)
        // Support multiple parameter names used by different gateways
        $sessionId = $_POST['sessionId'] ?? $_POST['SessionId'] ?? $_GET['sessionId'] ?? $_GET['SessionId'] ?? '';
        
        // Try different phone number parameter names
        $phoneNumber = $_POST['phoneNumber'] ?? $_POST['PhoneNumber'] ?? $_POST['msisdn'] ?? $_POST['MSISDN'] ?? 
                      $_GET['phoneNumber'] ?? $_GET['PhoneNumber'] ?? $_GET['msisdn'] ?? $_GET['MSISDN'] ?? '';
        
        // Try different text parameter names
        $text = $_POST['text'] ?? $_POST['Text'] ?? $_POST['input'] ?? $_POST['Input'] ?? 
               $_GET['text'] ?? $_GET['Text'] ?? $_GET['input'] ?? $_GET['Input'] ?? '';
        
        // Debug log - show all POST/GET parameters
        error_log('USSD Request - POST: ' . json_encode($_POST));
        error_log('USSD Request - GET: ' . json_encode($_GET));
        error_log('USSD Request - Raw Phone: ' . $phoneNumber . ', Text: ' . $text);
        
        // Clean phone number
        $phoneNumber = $this->cleanPhoneNumber($phoneNumber);
        
        // Debug log after cleaning
        error_log('USSD Request - Cleaned Phone: ' . $phoneNumber);
        
        // Get or create session
        $session = $this->sessionService->getOrCreateSession($sessionId, $phoneNumber);
        
        // Parse user input
        $textArray = explode('*', $text);
        $userInput = end($textArray);
        
        // If text is empty, this is the first dial - show main menu
        if (empty($text)) {
            $response = $this->menuService->buildMainMenu();
        } else {
            // Route to appropriate handler
            $response = $this->routeRequest($session, $userInput, $phoneNumber);
        }
        
        // Ensure response has CON or END prefix
        if (!preg_match('/^(CON|END)\s/', $response)) {
            error_log('USSD Response missing prefix: ' . $response);
            $response = 'CON ' . $response;
        }
        
        // Log response for debugging
        error_log('USSD Response: ' . substr($response, 0, 100));
        
        // Output response
        header('Content-Type: text/plain');
        echo $response;
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
                return $this->handleStationSelection($session->session_id, $userInput);
                
            case 'select_station_campaign':
                return $this->handleStationCampaignSelection($session->session_id, $userInput, $sessionData);
                
            case 'select_programme':
                return $this->handleProgrammeSelection($session->session_id, $userInput, $sessionData);
                
            case 'select_campaign':
                return $this->handleCampaignSelection($session->session_id, $userInput, $sessionData);
                
            case 'select_quantity':
                return $this->handleQuantitySelection($session->session_id, $userInput, $sessionData);
                
            case 'enter_custom_quantity':
                return $this->handleCustomQuantity($session->session_id, $userInput, $sessionData);
                
            case 'confirm_payment':
                return $this->handlePaymentConfirmation($session->session_id, $userInput, $sessionData, $phoneNumber);
                
            case 'select_payment_method':
                return $this->handlePaymentMethod($session->session_id, $userInput, $sessionData, $phoneNumber);
                
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
                $this->sessionService->updateSession($sessionId, 'select_station');
                return $this->menuService->buildStationMenu();
                
            case '2': // Check My Tickets
                $this->sessionService->updateSession($sessionId, 'view_tickets', ['ticket_page' => 1]);
                return $this->menuService->buildTicketList($phoneNumber, 1);
                
            case '3': // Check Winners
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
     * Handle station selection
     */
    private function handleStationSelection($sessionId, $input)
    {
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'main_menu');
            return $this->menuService->buildMainMenu();
        }
        
        $stations = $this->menuService->getStationsArray();
        $index = (int)$input - 1;
        
        if (!isset($stations[$index])) {
            return "Invalid selection. Please try again.\n" . $this->menuService->buildStationMenu();
        }
        
        $selectedStation = $stations[$index];
        $this->sessionService->updateSession($sessionId, 'select_station_campaign', [
            'station_id' => $selectedStation->id,
            'station_name' => $selectedStation->name
        ]);
        
        // Show station-wide campaigns with option to browse by programme
        return $this->menuService->buildStationCampaignMenu($selectedStation->id);
    }
    
    /**
     * Handle station campaign selection (station-wide campaigns or browse by programme)
     */
    private function handleStationCampaignSelection($sessionId, $input, $sessionData)
    {
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_station');
            return $this->menuService->buildStationMenu();
        }
        
        $stationId = $sessionData['station_id'];
        $campaigns = $this->menuService->getStationCampaignsArray($stationId);
        $index = (int)$input - 1;
        
        // Check if user selected "Browse by Programme" option
        $browseByProgrammeIndex = count($campaigns);
        
        if ($index == $browseByProgrammeIndex) {
            // User wants to browse by programme
            $this->sessionService->updateSession($sessionId, 'select_programme', [
                'station_id' => $stationId,
                'station_name' => $sessionData['station_name']
            ]);
            return $this->menuService->buildProgrammeMenu($stationId);
        }
        
        // User selected a station-wide campaign
        if (!isset($campaigns[$index])) {
            return "Invalid selection. Please try again.\n" . 
                   $this->menuService->buildStationCampaignMenu($stationId);
        }
        
        $selectedCampaign = $campaigns[$index];
        $this->sessionService->updateSession($sessionId, 'select_quantity', [
            'campaign_id' => $selectedCampaign->id,
            'campaign_name' => $selectedCampaign->name,
            'ticket_price' => $selectedCampaign->ticket_price,
            'station_id' => $stationId,
            'programme_id' => null // Station-wide campaign
        ]);
        
        return $this->menuService->buildQuantityMenu(
            $selectedCampaign->name,
            $selectedCampaign->ticket_price
        );
    }
    
    /**
     * Handle programme selection
     */
    private function handleProgrammeSelection($sessionId, $input, $sessionData)
    {
        if ($input == '0') {
            // Go back to station campaigns
            $this->sessionService->updateSession($sessionId, 'select_station_campaign');
            return $this->menuService->buildStationCampaignMenu($sessionData['station_id']);
        }
        
        $stationId = $sessionData['station_id'];
        $programmes = $this->menuService->getProgrammesArray($stationId);
        $index = (int)$input - 1;
        
        if (!isset($programmes[$index])) {
            return "Invalid selection. Please try again.\n" . 
                   $this->menuService->buildProgrammeMenu($stationId);
        }
        
        $selectedProgramme = $programmes[$index];
        $this->sessionService->updateSession($sessionId, 'select_campaign', [
            'programme_id' => $selectedProgramme->id,
            'programme_name' => $selectedProgramme->name
        ]);
        
        return $this->menuService->buildCampaignMenu($stationId, $selectedProgramme->id);
    }
    
    /**
     * Handle campaign selection
     */
    private function handleCampaignSelection($sessionId, $input, $sessionData)
    {
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_programme');
            return $this->menuService->buildProgrammeMenu($sessionData['station_id']);
        }
        
        $campaigns = $this->menuService->getCampaignsArray(
            $sessionData['station_id'],
            $sessionData['programme_id']
        );
        $index = (int)$input - 1;
        
        if (!isset($campaigns[$index])) {
            return "Invalid selection. Please try again.\n" . 
                   $this->menuService->buildCampaignMenu(
                       $sessionData['station_id'],
                       $sessionData['programme_id']
                   );
        }
        
        $selectedCampaign = $campaigns[$index];
        $this->sessionService->updateSession($sessionId, 'select_quantity', [
            'campaign_id' => $selectedCampaign->id,
            'campaign_name' => $selectedCampaign->name,
            'ticket_price' => $selectedCampaign->ticket_price
        ]);
        
        return $this->menuService->buildQuantityMenu(
            $selectedCampaign->name,
            $selectedCampaign->ticket_price
        );
    }
    
    /**
     * Handle quantity selection
     */
    private function handleQuantitySelection($sessionId, $input, $sessionData)
    {
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_campaign');
            return $this->menuService->buildCampaignMenu(
                $sessionData['station_id'],
                $sessionData['programme_id']
            );
        }
        
        $ticketPrice = $sessionData['ticket_price'];
        $quantity = 0;
        
        switch ($input) {
            case '1':
                $quantity = 1;
                break;
            case '2':
                $quantity = 2;
                break;
            case '3':
                $quantity = 3;
                break;
            case '4':
                $quantity = 5;
                break;
            case '5':
                $this->sessionService->updateSession($sessionId, 'enter_custom_quantity');
                return "CON Enter number of Entries (1-100):";
            default:
                return "CON Invalid selection.\n" . 
                       substr($this->menuService->buildQuantityMenu(
                           $sessionData['campaign_name'],
                           $ticketPrice
                       ), 4); // Remove CON prefix to avoid duplication
        }
        
        $totalAmount = $quantity * $ticketPrice;
        $this->sessionService->updateSession($sessionId, 'confirm_payment', [
            'quantity' => $quantity,
            'total_amount' => $totalAmount
        ]);
        
        return $this->menuService->buildPaymentConfirmation(
            $quantity,
            $totalAmount,
            $sessionData['phone_number'] ?? ''
        );
    }
    
    /**
     * Handle custom quantity input
     */
    private function handleCustomQuantity($sessionId, $input, $sessionData)
    {
        $quantity = (int)$input;
        
        if ($quantity < 1 || $quantity > 100) {
            return "CON Invalid quantity. Enter 1-100:";
        }
        
        $ticketPrice = $sessionData['ticket_price'];
        $totalAmount = $quantity * $ticketPrice;
        
        $this->sessionService->updateSession($sessionId, 'confirm_payment', [
            'quantity' => $quantity,
            'total_amount' => $totalAmount
        ]);
        
        return $this->menuService->buildPaymentConfirmation(
            $quantity,
            $totalAmount,
            $sessionData['phone_number'] ?? ''
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
            return $this->menuService->buildPaymentMethodMenu();
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
        
        $gateway = '';
        $isManual = false;
        $useHubtel = false;
        
        switch ($input) {
            case '1':
                $gateway = 'hubtel';
                $useHubtel = true;
                break;
            case '2':
                $gateway = 'manual';
                $isManual = true;
                break;
            default:
                return "CON Invalid selection.\n" . substr($this->menuService->buildPaymentMethodMenu(), 4);
        }
        
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
        
        // If Hubtel payment, initiate mobile money payment
        if ($useHubtel) {
            require_once '../app/services/PaymentGateway/HubtelService.php';
            $hubtelService = new \App\Services\PaymentGateway\HubtelService();
            
            // Get campaign for description
            $campaignModel = $this->model('Campaign');
            $campaign = $campaignModel->findById($sessionData['campaign_id']);
            
            // Initiate Hubtel payment
            $hubtelData = [
                'phone' => $phoneNumber,
                'amount' => $sessionData['total_amount'],
                'reference' => $reference,
                'player_name' => $player->name ?? 'USSD User',
                'description' => 'Raffle Ticket: ' . ($campaign->name ?? 'Campaign'),
                'callback_url' => $this->getCallbackUrl()
            ];
            
            // Debug log
            error_log('Hubtel payment data: ' . json_encode($hubtelData));
            
            $hubtelResponse = $hubtelService->initiatePayment($hubtelData);
            
            if ($hubtelResponse['success']) {
                // Update payment with gateway reference
                $this->paymentModel->update($paymentId, [
                    'gateway_reference' => $hubtelResponse['gateway_reference'] ?? $reference,
                    'status' => $hubtelResponse['status'] // 'pending' or 'success'
                ]);
                
                // Close session
                $this->sessionService->closeSession($sessionId);
                
                // Return success message
                return "END Amt: GHS " . number_format($sessionData['total_amount'], 2) . "\n" .
                       "Qty: {$sessionData['quantity']} ticket(s)\n\n" .
                       "Approve prompt on your phone.";
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
        
        // If manual payment, process immediately
        if ($isManual) {
            // Update payment to success
            $this->paymentModel->update($paymentId, [
                'status' => 'success',
                'paid_at' => date('Y-m-d H:i:s')
            ]);
            
            // Generate tickets
            require_once '../app/services/TicketGeneratorService.php';
            require_once '../app/services/RevenueAllocationService.php';
            
            $ticketService = new \App\Services\TicketGeneratorService();
            $revenueService = new \App\Services\RevenueAllocationService();
            
            $campaignModel = $this->model('Campaign');
            $campaign = $campaignModel->findById($sessionData['campaign_id']);
            
            // Prepare payment data for services
            $paymentData = [
                'payment_id' => $paymentId,
                'player_id' => $playerId,
                'campaign_id' => $sessionData['campaign_id'],
                'station_id' => $sessionData['station_id'],
                'programme_id' => $sessionData['programme_id'] ?? null, // Can be null for station-wide campaigns
                'amount' => $sessionData['total_amount']
            ];
            
            // Generate tickets
            $ticketResult = $ticketService->generateTickets($paymentData);
            
            // Allocate revenue
            $revenueService->allocate($paymentData);
            
            // Close session
            $this->sessionService->closeSession($sessionId);
            
            // Return success with ticket codes
            if ($ticketResult && isset($ticketResult['tickets'])) {
                $ticketCodes = array_map(function($t) { 
                    return is_array($t) ? $t['ticket_code'] : $t->ticket_code; 
                }, $ticketResult['tickets']);
                
                return "END Payment Successful!\n" .
                       "Amount: GHS " . number_format($sessionData['total_amount'], 2) . "\n" .
                       "Quantity: {$sessionData['quantity']} ticket(s)\n" .
                       "Code: " . implode(', ', $ticketCodes) . "\n\n" .
                       "Good luck!";
            }
            
            return "END Payment processed but ticket generation failed.\n" .
                   "Please contact support.\n" .
                   "Reference: PAY{$paymentId}";
        }
        
        // This should not be reached anymore (all cases handled above)
        $this->sessionService->closeSession($sessionId);
        return "END Payment processing error. Please try again.";
    }
    
    /**
     * Clean phone number to standard format
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
