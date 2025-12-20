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
    private $campaignModel;
    
    public function __construct()
    {
        $this->sessionService = new UssdSessionService();
        $this->menuService = new UssdMenuService();
        $this->playerModel = $this->model('Player');
        $this->ticketModel = $this->model('Ticket');
        $this->paymentModel = $this->model('Payment');
        $this->campaignModel = $this->model('Campaign');
    }
    
    /**
     * Main USSD entry point - Service Interaction URL
     * Handles all USSD interactions according to Hubtel Programmable Services API
     */
    public function index()
    {
        // Get raw JSON input from Hubtel
        $rawInput = file_get_contents('php://input');
        error_log('=== USSD REQUEST ===' . PHP_EOL . $rawInput);
        
        // Decode JSON request from Hubtel
        $request = json_decode($rawInput, true);
        
        if (!$request) {
            error_log('USSD Error: Invalid JSON received');
            $this->sendErrorResponse('', 'Service temporarily unavailable.');
            return;
        }
        
        // Check if this is a Service Fulfillment request (has OrderId)
        if (isset($request['OrderId'])) {
            error_log("USSD: Detected Service Fulfillment request");
            return $this->handleServiceFulfillment();
        }
        
        // Extract Hubtel parameters according to API spec
        $sessionId = $request['SessionId'] ?? '';
        $phoneNumber = $request['Mobile'] ?? '';
        $message = $request['Message'] ?? '';
        $type = $request['Type'] ?? ''; // Initiation, Response, Timeout
        $sequence = $request['Sequence'] ?? 1;
        $clientState = $request['ClientState'] ?? '';
        $serviceCode = $request['ServiceCode'] ?? '';
        $operator = $request['Operator'] ?? '';
        $platform = $request['Platform'] ?? 'USSD';
        
        error_log("USSD Request - Type: $type, SessionId: $sessionId, Phone: $phoneNumber, Message: $message, Sequence: $sequence, Platform: $platform");
        
        // Validate required fields
        if (empty($sessionId) || empty($phoneNumber)) {
            error_log('USSD Error: Missing required fields (SessionId or Mobile)');
            $this->sendErrorResponse($sessionId, 'Invalid request. Please try again.');
            return;
        }
        
        // Clean phone number to standard format
        $phoneNumber = $this->cleanPhoneNumber($phoneNumber);
        
        // Handle different request types
        try {
            if ($type === 'Initiation') {
                $this->handleInitiation($sessionId, $phoneNumber, $platform, $operator);
            } elseif ($type === 'Timeout') {
                $this->handleTimeout($sessionId);
            } elseif ($type === 'Response') {
                $this->handleResponse($sessionId, $phoneNumber, $message, $clientState, $sequence, $platform);
            } else {
                error_log("USSD Error: Unknown request type: $type");
                $this->sendErrorResponse($sessionId, 'Invalid request type.');
            }
        } catch (\Exception $e) {
            error_log('USSD Exception: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            $this->sendErrorResponse($sessionId, 'An error occurred. Please try again.');
        }
    }
    
    /**
     * Handle Initiation request - first interaction
     */
    private function handleInitiation($sessionId, $phoneNumber, $platform, $operator)
    {
        error_log("USSD Initiation - SessionId: $sessionId, Phone: $phoneNumber, Platform: $platform");
        
        // Create new session
        $session = $this->sessionService->getOrCreateSession($sessionId, $phoneNumber);
        
        // Store platform and operator info
        $this->sessionService->updateSession($sessionId, 'main_menu', [
            'platform' => $platform,
            'operator' => $operator
        ]);
        
        // Show main menu
        $menuText = $this->menuService->buildMainMenu();
        $this->sendResponse(
            $sessionId,
            'response',
            substr($menuText, 4), // Remove "CON " prefix
            'Main Menu',
            'input',
            'text',
            'main_menu' // ClientState to track current step
        );
    }
    
    /**
     * Handle Timeout request - user took too long
     */
    private function handleTimeout($sessionId)
    {
        error_log("USSD Timeout - SessionId: $sessionId");
        
        // Close session
        $this->sessionService->closeSession($sessionId);
        
        // Send release response
        $this->sendResponse(
            $sessionId,
            'release',
            'Session timed out. Please dial again to continue.',
            'Timeout',
            'display',
            'text'
        );
    }
    
    /**
     * Handle Response request - user provided input
     */
    private function handleResponse($sessionId, $phoneNumber, $userInput, $clientState, $sequence, $platform)
    {
        error_log("USSD Response - SessionId: $sessionId, Input: $userInput, ClientState: $clientState, Sequence: $sequence");
        
        // Get session
        $session = $this->sessionService->getSession($sessionId);
        
        if (!$session) {
            error_log("USSD Error: Session not found - $sessionId");
            $this->sendErrorResponse($sessionId, 'Session expired. Please dial again.');
            return;
        }
        
        // Use ClientState if provided, otherwise use session's current_step
        $currentStep = !empty($clientState) ? $clientState : $session->current_step;
        $sessionData = json_decode($session->session_data, true) ?: [];
        
        // Route to appropriate handler
        $this->routeRequest($sessionId, $currentStep, $userInput, $phoneNumber, $sessionData, $platform);
    }
    
    /**
     * Send response in Hubtel Programmable Services API format
     * 
     * @param string $sessionId - Unique session identifier
     * @param string $type - response|release|AddToCart
     * @param string $message - Text to display (supports \n for new lines)
     * @param string $label - Title for Web/Mobile channels
     * @param string $dataType - display|input
     * @param string $fieldType - text|phone|email|number|decimal|textarea
     * @param string $clientState - State data to pass back in next request
     * @param array $item - Item data for AddToCart type
     */
    private function sendResponse($sessionId, $type, $message, $label, $dataType = 'display', $fieldType = 'text', $clientState = '', $item = null)
    {
        // Build response according to Hubtel API spec
        $response = [
            'SessionId' => $sessionId,
            'Type' => $type,
            'Message' => $message,
            'Label' => $label,
            'DataType' => $dataType,
            'FieldType' => $fieldType
        ];
        
        // Add ClientState if provided (for session continuity)
        if (!empty($clientState)) {
            $response['ClientState'] = $clientState;
        }
        
        // Add Item for AddToCart type
        if ($type === 'AddToCart' && $item !== null) {
            $response['Item'] = $item;
        }
        
        // Remove DataType and FieldType for release type (optional per API)
        if ($type === 'release') {
            // Keep them for consistency, but they're optional
        }
        
        error_log('=== USSD RESPONSE ===' . PHP_EOL . json_encode($response, JSON_PRETTY_PRINT));
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit;
    }
    
    /**
     * Send error response
     */
    private function sendErrorResponse($sessionId, $message)
    {
        $this->sendResponse(
            $sessionId,
            'release',
            $message,
            'Error',
            'display',
            'text'
        );
    }
    
    /**
     * Send AddToCart response for Hubtel payment collection
     * This triggers the checkout flow and payment prompt
     * 
     * NOTE: Despite API documentation suggesting Qty × Price calculation,
     * Hubtel actually charges the Price field directly as the total amount.
     * Therefore, we send total amount in Price field and Qty as 1.
     * 
     * Merchant absorbs transaction fees - customer pays base amount only.
     * 
     * Item must contain:
     * - ItemName: string (campaign name with quantity)
     * - Qty: integer (always 1)
     * - Price: float (total amount to charge)
     */
    private function sendAddToCartResponse($sessionId, $campaignName, $quantity, $unitPrice, $totalAmount)
    {
        error_log("AddToCart - Amount: $totalAmount (merchant absorbs fees)");
        
        $item = [
            'ItemName' => $campaignName,
            'Qty' => 1,
            'Price' => (float)$totalAmount
        ];
        
        $message = "Confirm:\n\n" .
                   "Item: {$campaignName}\n" .
                   "Entries: {$quantity}\n" .
                   "Price: GHS " . number_format($unitPrice, 2) . "\n" .
                   "Total: GHS " . number_format($totalAmount, 2) . "\n\n" .
                   "Approve prompt or Dial *170#";
        
        $this->sendResponse(
            $sessionId,
            'AddToCart',
            $message,
            'Payment Checkout',
            'display',
            'text',
            '', // No ClientState needed - session ends after AddToCart
            $item
        );
    }
    
    /**
     * Route request based on current step
     * Updated to work with new response structure
     */
    private function routeRequest($sessionId, $currentStep, $userInput, $phoneNumber, $sessionData, $platform)
    {
        error_log("Routing request - Step: $currentStep, Input: $userInput");
        
        switch ($currentStep) {
            case 'main_menu':
                $this->handleMainMenu($sessionId, $userInput, $phoneNumber, $platform);
                break;
                
            case 'select_station':
                $this->handleStationSelection($sessionId, $userInput, $sessionData, $platform);
                break;
                
            case 'select_station_campaign':
                $this->handleStationCampaignSelection($sessionId, $userInput, $sessionData, $phoneNumber, $platform);
                break;
                
            case 'select_programme':
                $this->handleProgrammeSelection($sessionId, $userInput, $sessionData, $platform);
                break;
                
            case 'select_campaign':
                $this->handleCampaignSelection($sessionId, $userInput, $sessionData, $phoneNumber, $platform);
                break;
                
            case 'select_quantity':
                $this->handleQuantitySelection($sessionId, $userInput, $sessionData, $platform);
                break;
                
            case 'confirm_payment':
                $this->handlePaymentConfirmation($sessionId, $userInput, $sessionData, $phoneNumber, $platform);
                break;
                
            case 'view_tickets':
                $this->handleTicketNavigation($sessionId, $userInput, $sessionData, $phoneNumber, $platform);
                break;
                
            default:
                error_log("Unknown step: $currentStep, showing main menu");
                $menuText = $this->menuService->buildMainMenu();
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'Main Menu',
                    'input',
                    'text',
                    'main_menu'
                );
        }
    }
    
    /**
     * Handle main menu selection
     */
    private function handleMainMenu($sessionId, $input, $phoneNumber, $platform)
    {
        switch ($input) {
            case '1': // Buy Ticket
                $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => 1]);
                $menuText = $this->menuService->buildStationMenu(1);
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'Select Platform',
                    'input',
                    'text',
                    'select_station'
                );
                break;
                
            case '2': // Check My Tickets
                $this->sessionService->updateSession($sessionId, 'view_tickets', ['ticket_page' => 1]);
                $menuText = $this->menuService->buildTicketList($phoneNumber, 1);
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'My Tickets',
                    'input',
                    'text',
                    'view_tickets'
                );
                break;
                
            case '3': // Check Winnings
                $this->sessionService->closeSession($sessionId);
                $menuText = $this->menuService->buildWinnerCheck($phoneNumber);
                $this->sendResponse(
                    $sessionId,
                    'release',
                    substr($menuText, 4),
                    'Winnings',
                    'display',
                    'text'
                );
                break;
                
            case '4': // My Balance
                $this->sessionService->closeSession($sessionId);
                $menuText = $this->menuService->buildBalanceInquiry($phoneNumber);
                $this->sendResponse(
                    $sessionId,
                    'release',
                    substr($menuText, 4),
                    'Balance',
                    'display',
                    'text'
                );
                break;
                
            case '0': // Exit
                $this->sessionService->closeSession($sessionId);
                $this->sendResponse(
                    $sessionId,
                    'release',
                    'Thank you for using Raffle System!',
                    'Goodbye',
                    'display',
                    'text'
                );
                break;
                
            default:
                $menuText = $this->menuService->buildMainMenu();
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'Main Menu',
                    'input',
                    'text',
                    'main_menu'
                );
        }
    }
    
    /**
     * Handle station selection with pagination
     */
    private function handleStationSelection($sessionId, $input, $sessionData = [], $platform = 'USSD')
    {
        $currentPage = $sessionData['station_page'] ?? 1;
        
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'main_menu');
            $menuText = $this->menuService->buildMainMenu();
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Main Menu',
                'input',
                'text',
                'main_menu'
            );
            return;
        }
        
        // Handle pagination
        if ($input == '5') {
            // Next page
            $newPage = $currentPage + 1;
            $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => $newPage]);
            $menuText = $this->menuService->buildStationMenu($newPage);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Select Platform',
                'input',
                'text',
                'select_station'
            );
            return;
        }
        
        if ($input == '6') {
            // Previous page
            $newPage = max(1, $currentPage - 1);
            $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => $newPage]);
            $menuText = $this->menuService->buildStationMenu($newPage);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Select Platform',
                'input',
                'text',
                'select_station'
            );
            return;
        }
        
        // Get stations for current page
        $perPage = 4;
        $offset = ($currentPage - 1) * $perPage;
        $stations = $this->menuService->getStationsArray($offset, $perPage);
        $index = (int)$input - 1;
        
        if (!isset($stations[$index])) {
            $menuText = $this->menuService->buildStationMenu($currentPage);
            $this->sendResponse(
                $sessionId,
                'response',
                "Invalid selection. Please try again.\n\n" . substr($menuText, 4),
                'Select Platform',
                'input',
                'text',
                'select_station'
            );
            return;
        }
        
        $selectedStation = $stations[$index];
        $this->sessionService->updateSession($sessionId, 'select_station_campaign', [
            'station_id' => $selectedStation->id,
            'station_name' => $selectedStation->name,
            'campaign_page' => 1
        ]);
        
        // Show station-wide campaigns with option to browse by programme
        $menuText = $this->menuService->buildStationCampaignMenu($selectedStation->id, 1);
        $this->sendResponse(
            $sessionId,
            'response',
            substr($menuText, 4),
            $selectedStation->name . ' - Campaigns',
            'input',
            'text',
            'select_station_campaign'
        );
    }
    
    /**
     * Handle station campaign selection (station-wide campaigns or browse by programme)
     */
    private function handleStationCampaignSelection($sessionId, $input, $sessionData, $phoneNumber, $platform)
    {
        $currentPage = $sessionData['campaign_page'] ?? 1;
        $stationId = $sessionData['station_id'];
        
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_station', ['station_page' => 1]);
            $menuText = $this->menuService->buildStationMenu(1);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Select Platform',
                'input',
                'text',
                'select_station'
            );
            return;
        }
        
        // Handle pagination
        if ($input == '5') {
            $newPage = $currentPage + 1;
            $this->sessionService->updateSession($sessionId, 'select_station_campaign', array_merge($sessionData, ['campaign_page' => $newPage]));
            $menuText = $this->menuService->buildStationCampaignMenu($stationId, $newPage);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Campaigns',
                'input',
                'text',
                'select_station_campaign'
            );
            return;
        }
        
        if ($input == '6') {
            $newPage = max(1, $currentPage - 1);
            $this->sessionService->updateSession($sessionId, 'select_station_campaign', array_merge($sessionData, ['campaign_page' => $newPage]));
            $menuText = $this->menuService->buildStationCampaignMenu($stationId, $newPage);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Campaigns',
                'input',
                'text',
                'select_station_campaign'
            );
            return;
        }
        
        // Check if user selected "Filter by Programme" option (option 7)
        if ($input == '7') {
            $this->sessionService->updateSession($sessionId, 'select_programme', [
                'station_id' => $stationId,
                'station_name' => $sessionData['station_name'],
                'programme_page' => 1
            ]);
            $menuText = $this->menuService->buildProgrammeMenu($stationId, 1);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Select Programme',
                'input',
                'text',
                'select_programme'
            );
            return;
        }
        
        // Get campaigns for current page
        $perPage = 4;
        $offset = ($currentPage - 1) * $perPage;
        $campaigns = $this->menuService->getStationCampaignsArray($stationId, $offset, $perPage);
        $index = (int)$input - 1;
        
        // User selected a campaign
        if (!isset($campaigns[$index])) {
            $menuText = $this->menuService->buildStationCampaignMenu($stationId, $currentPage);
            $this->sendResponse(
                $sessionId,
                'response',
                "Invalid selection. Please try again.\n\n" . substr($menuText, 4),
                'Campaigns',
                'input',
                'text',
                'select_station_campaign'
            );
            return;
        }
        
        $selectedCampaign = $campaigns[$index];
        $this->sessionService->updateSession($sessionId, 'select_quantity', array_merge($sessionData, [
            'campaign_id' => $selectedCampaign->id,
            'campaign_name' => $selectedCampaign->name,
            'ticket_price' => $selectedCampaign->ticket_price,
            'station_id' => $stationId,
            'programme_id' => null,
            'phone_number' => $phoneNumber
        ]));
        
        $menuText = $this->menuService->buildQuantityMenu(
            $selectedCampaign->name,
            $selectedCampaign->ticket_price
        );
        $this->sendResponse(
            $sessionId,
            'response',
            substr($menuText, 4),
            'Enter Quantity',
            'input',
            'number',
            'select_quantity'
        );
    }
    
    /**
     * Handle programme selection with pagination
     */
    private function handleProgrammeSelection($sessionId, $input, $sessionData, $platform)
    {
        $currentPage = $sessionData['programme_page'] ?? 1;
        $stationId = $sessionData['station_id'];
        
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_station_campaign', [
                'station_id' => $stationId,
                'station_name' => $sessionData['station_name'] ?? '',
                'campaign_page' => 1
            ]);
            $menuText = $this->menuService->buildStationCampaignMenu($stationId);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Campaigns',
                'input',
                'text',
                'select_station_campaign'
            );
            return;
        }
        
        // Handle pagination
        if ($input == '5') {
            $newPage = $currentPage + 1;
            $this->sessionService->updateSession($sessionId, 'select_programme', array_merge($sessionData, ['programme_page' => $newPage]));
            $menuText = $this->menuService->buildProgrammeMenu($stationId, $newPage);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Select Programme',
                'input',
                'text',
                'select_programme'
            );
            return;
        }
        
        if ($input == '6') {
            $newPage = max(1, $currentPage - 1);
            $this->sessionService->updateSession($sessionId, 'select_programme', array_merge($sessionData, ['programme_page' => $newPage]));
            $menuText = $this->menuService->buildProgrammeMenu($stationId, $newPage);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Select Programme',
                'input',
                'text',
                'select_programme'
            );
            return;
        }
        
        // Get programmes for current page
        $perPage = 4;
        $offset = ($currentPage - 1) * $perPage;
        $programmes = $this->menuService->getProgrammesArray($stationId, $offset, $perPage);
        $index = (int)$input - 1;
        
        if (!isset($programmes[$index])) {
            $menuText = $this->menuService->buildProgrammeMenu($stationId, $currentPage);
            $this->sendResponse(
                $sessionId,
                'response',
                "Invalid selection. Please try again.\n\n" . substr($menuText, 4),
                'Select Programme',
                'input',
                'text',
                'select_programme'
            );
            return;
        }
        
        $selectedProgramme = $programmes[$index];
        $this->sessionService->updateSession($sessionId, 'select_campaign', array_merge($sessionData, [
            'programme_id' => $selectedProgramme->id,
            'programme_name' => $selectedProgramme->name
        ]));
        
        $menuText = $this->menuService->buildCampaignMenu($stationId, $selectedProgramme->id);
        $this->sendResponse(
            $sessionId,
            'response',
            substr($menuText, 4),
            $selectedProgramme->name . ' - Campaigns',
            'input',
            'text',
            'select_campaign'
        );
    }
    
    /**
     * Handle campaign selection
     */
    private function handleCampaignSelection($sessionId, $input, $sessionData, $phoneNumber, $platform)
    {
        if ($input == '0') {
            $this->sessionService->updateSession($sessionId, 'select_programme', [
                'station_id' => $sessionData['station_id'],
                'station_name' => $sessionData['station_name'] ?? '',
                'programme_page' => 1
            ]);
            $menuText = $this->menuService->buildProgrammeMenu($sessionData['station_id'], 1);
            $this->sendResponse(
                $sessionId,
                'response',
                substr($menuText, 4),
                'Select Programme',
                'input',
                'text',
                'select_programme'
            );
            return;
        }
        
        $campaigns = $this->menuService->getCampaignsArray(
            $sessionData['station_id'],
            $sessionData['programme_id']
        );
        $index = (int)$input - 1;
        
        if (!isset($campaigns[$index])) {
            $menuText = $this->menuService->buildCampaignMenu(
                $sessionData['station_id'],
                $sessionData['programme_id']
            );
            $this->sendResponse(
                $sessionId,
                'response',
                "Invalid selection. Please try again.\n\n" . substr($menuText, 4),
                'Select Campaign',
                'input',
                'text',
                'select_campaign'
            );
            return;
        }
        
        $selectedCampaign = $campaigns[$index];
        $this->sessionService->updateSession($sessionId, 'select_quantity', array_merge($sessionData, [
            'campaign_id' => $selectedCampaign->id,
            'campaign_name' => $selectedCampaign->name,
            'ticket_price' => $selectedCampaign->ticket_price,
            'phone_number' => $phoneNumber
        ]));
        
        $menuText = $this->menuService->buildQuantityMenu(
            $selectedCampaign->name,
            $selectedCampaign->ticket_price
        );
        $this->sendResponse(
            $sessionId,
            'response',
            substr($menuText, 4),
            'Enter Quantity',
            'input',
            'number',
            'select_quantity'
        );
    }
    
    /**
     * Handle quantity selection (direct number input)
     */
    private function handleQuantitySelection($sessionId, $input, $sessionData, $platform)
    {
        $quantity = (int)$input;
        $ticketPrice = $sessionData['ticket_price'];
        
        // Validate quantity
        if ($quantity < 1 || $quantity > 1000) {
            $menuText = $this->menuService->buildQuantityMenu(
                $sessionData['campaign_name'],
                $ticketPrice
            );
            $this->sendResponse(
                $sessionId,
                'response',
                "Invalid quantity. Enter 1-1000:\n\n" . substr($menuText, 4),
                'Enter Quantity',
                'input',
                'number',
                'select_quantity'
            );
            return;
        }
        
        $totalAmount = $quantity * $ticketPrice;
        
        $this->sessionService->updateSession($sessionId, 'confirm_payment', array_merge($sessionData, [
            'quantity' => $quantity,
            'total_amount' => $totalAmount
        ]));
        
        $menuText = $this->menuService->buildPaymentConfirmation(
            $quantity,
            $totalAmount,
            $this->formatPhoneForDisplay($sessionData['phone_number'] ?? '')
        );
        $this->sendResponse(
            $sessionId,
            'response',
            substr($menuText, 4),
            'Confirm Payment',
            'input',
            'text',
            'confirm_payment'
        );
    }
    
    /**
     * Handle payment confirmation
     * User confirms they want to proceed with payment
     */
    private function handlePaymentConfirmation($sessionId, $input, $sessionData, $phoneNumber, $platform)
    {
        if ($input == '0') {
            // Cancel purchase
            $this->sessionService->closeSession($sessionId);
            $this->sendResponse(
                $sessionId,
                'release',
                'Purchase cancelled. Thank you!',
                'Cancelled',
                'display',
                'text'
            );
            return;
        }
        
        if ($input == '1') {
            // Proceed with payment using AddToCart
            $this->initiateAddToCartPayment($sessionId, $sessionData, $phoneNumber);
            return;
        }
        
        // Invalid input
        $menuText = $this->menuService->buildPaymentConfirmation(
            $sessionData['quantity'],
            $sessionData['total_amount'],
            $this->formatPhoneForDisplay($phoneNumber)
        );
        $this->sendResponse(
            $sessionId,
            'response',
            "Invalid selection. Please try again.\n\n" . substr($menuText, 4),
            'Confirm Payment',
            'input',
            'text',
            'confirm_payment'
        );
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
     * Initiate payment using Hubtel's AddToCart feature
     * This triggers the checkout flow and payment prompt
     * 
     * According to Hubtel API:
     * 1. Send AddToCart response with Item details
     * 2. Hubtel collects payment from user
     * 3. Hubtel sends Service Fulfillment callback with OrderInfo
     */
    private function initiateAddToCartPayment($sessionId, $sessionData, $phoneNumber)
    {
        error_log("=== INITIATING ADDTOCART PAYMENT ===");
        
        // Validate required data
        if (empty($phoneNumber)) {
            error_log("Error: Phone number not available");
            $this->sendErrorResponse($sessionId, 'Error: Phone number not available. Please try again.');
            return;
        }
        
        if (empty($sessionData['campaign_id']) || empty($sessionData['quantity']) || empty($sessionData['ticket_price'])) {
            error_log("Error: Missing required session data");
            $this->sendErrorResponse($sessionId, 'Error: Session data incomplete. Please start again.');
            return;
        }
        
        // Get or create player
        $player = $this->playerModel->getByPhone($phoneNumber);
        if (!$player) {
            $playerId = $this->playerModel->create([
                'name' => 'USSD User',
                'phone' => $phoneNumber
            ]);
            $player = $this->playerModel->findById($playerId);
        } else {
            $playerId = $player->id;
        }
        
        // Create payment record with pending status
        // This will be updated when Service Fulfillment callback is received
        $reference = 'USSD' . time() . rand(1000, 9999);
        
        $paymentData = [
            'player_id' => $playerId,
            'campaign_id' => $sessionData['campaign_id'],
            'station_id' => $sessionData['station_id'],
            'programme_id' => $sessionData['programme_id'] ?? null,
            'amount' => $sessionData['total_amount'],
            'gateway' => 'hubtel',
            'gateway_reference' => $sessionId, // Use SessionId as reference for tracking
            'internal_reference' => $reference,
            'status' => 'pending',
            'channel' => 'ussd'
        ];
        
        $paymentId = $this->paymentModel->create($paymentData);
        
        error_log("Payment record created - ID: $paymentId, Reference: $reference, SessionId: $sessionId");
        
        // Update session with payment info for Service Fulfillment callback
        $this->sessionService->updateSession($sessionId, 'awaiting_payment', array_merge($sessionData, [
            'payment_id' => $paymentId,
            'payment_reference' => $reference,
            'player_id' => $playerId
        ]));
        
        // Send AddToCart response
        // Hubtel will handle payment collection and send Service Fulfillment callback
        $this->sendAddToCartResponse(
            $sessionId,
            $sessionData['campaign_name'],
            $sessionData['quantity'],
            $sessionData['ticket_price'],
            $sessionData['total_amount']
        );
    }
    
    /**
     * Process payment with Hubtel (fallback method)
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
                
                // Close session immediately to allow mobile money prompt
                $this->sessionService->closeSession($sessionId);
                
                // Return END message to close USSD session
                // This frees the phone to receive mobile money prompt
                return "END Payment initiated!\n\n" .
                       "₵" . number_format($sessionData['total_amount'], 2) . " for {$sessionData['quantity']} entries\n\n" .
                       "Approve the prompt or Dial *170#";
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
    private function handleTicketNavigation($sessionId, $input, $sessionData, $phoneNumber, $platform)
    {
        $currentPage = $sessionData['ticket_page'] ?? 1;
        
        switch ($input) {
            case '1': // Next Page
                $newPage = $currentPage + 1;
                $this->sessionService->updateSession($sessionId, 'view_tickets', ['ticket_page' => $newPage]);
                $menuText = $this->menuService->buildTicketList($phoneNumber, $newPage);
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'My Tickets',
                    'input',
                    'text',
                    'view_tickets'
                );
                break;
                
            case '2': // Previous Page
                $newPage = max(1, $currentPage - 1);
                $this->sessionService->updateSession($sessionId, 'view_tickets', ['ticket_page' => $newPage]);
                $menuText = $this->menuService->buildTicketList($phoneNumber, $newPage);
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'My Tickets',
                    'input',
                    'text',
                    'view_tickets'
                );
                break;
                
            case '0': // Back to Main Menu
                $this->sessionService->updateSession($sessionId, 'main_menu', []);
                $menuText = $this->menuService->buildMainMenu();
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'Main Menu',
                    'input',
                    'text',
                    'main_menu'
                );
                break;
                
            default:
                $menuText = $this->menuService->buildTicketList($phoneNumber, $currentPage);
                $this->sendResponse(
                    $sessionId,
                    'response',
                    substr($menuText, 4),
                    'My Tickets',
                    'input',
                    'text',
                    'view_tickets'
                );
        }
    }
    
    /**
     * Handle Service Fulfillment callback from Hubtel
     * This is called after user completes payment via AddToCart
     * 
     * According to Hubtel API, the payload contains:
     * - SessionId: Unique session identifier
     * - OrderId: Unique order identifier from Hubtel
     * - ExtraData: Additional data (optional)
     * - OrderInfo: Object containing payment details
     * 
     * After processing, we must send Service Fulfillment Callback to Hubtel
     */
    public function handleServiceFulfillment()
    {
        try {
            // Get JSON payload from Hubtel
            $rawInput = file_get_contents('php://input');
            error_log("=== SERVICE FULFILLMENT REQUEST ===" . PHP_EOL . $rawInput);
            
            $payload = json_decode($rawInput, true);
            
            if (!$payload) {
                error_log("Service Fulfillment Error: Invalid JSON");
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON payload']);
                return;
            }
            
            // Extract required fields according to API spec
            $sessionId = $payload['SessionId'] ?? null;
            $orderId = $payload['OrderId'] ?? null;
            $orderInfo = $payload['OrderInfo'] ?? null;
            $extraData = $payload['ExtraData'] ?? [];
            
            if (!$sessionId || !$orderId || !$orderInfo) {
                error_log("Service Fulfillment Error: Missing required fields");
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields: SessionId, OrderId, or OrderInfo']);
                return;
            }
            
            // Extract OrderInfo fields
            $paymentStatus = $orderInfo['Status'] ?? null; // 'Paid', 'Unpaid', etc.
            $paymentInfo = $orderInfo['Payment'] ?? null;
            $customerMobile = $orderInfo['CustomerMobileNumber'] ?? null;
            $customerName = $orderInfo['CustomerName'] ?? null;
            $items = $orderInfo['Items'] ?? [];
            $subtotal = $orderInfo['Subtotal'] ?? 0;
            
            error_log("Service Fulfillment - SessionId: $sessionId, OrderId: $orderId, Status: $paymentStatus");
            
            // Verify payment was successful
            if ($paymentStatus !== 'Paid') {
                error_log("Service Fulfillment: Payment not successful - Status: $paymentStatus");
                // Send failure callback to Hubtel
                $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'failed', [
                    'reason' => 'Payment status is not Paid'
                ]);
                
                http_response_code(200); // Still return 200 to acknowledge receipt
                echo json_encode(['status' => 'acknowledged', 'message' => 'Payment not successful']);
                return;
            }
            
            if (!$paymentInfo || !isset($paymentInfo['IsSuccessful']) || !$paymentInfo['IsSuccessful']) {
                error_log("Service Fulfillment: Payment not successful - IsSuccessful: false");
                $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'failed', [
                    'reason' => 'Payment IsSuccessful is false'
                ]);
                
                http_response_code(200);
                echo json_encode(['status' => 'acknowledged', 'message' => 'Payment not successful']);
                return;
            }
            
            // Get session data
            $session = $this->sessionService->getSession($sessionId);
            if (!$session) {
                error_log("Service Fulfillment Error: Session not found - $sessionId");
                $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'failed', [
                    'reason' => 'Session not found'
                ]);
                
                http_response_code(200);
                echo json_encode(['status' => 'acknowledged', 'message' => 'Session not found']);
                return;
            }
            
            $sessionData = json_decode($session->session_data, true) ?: [];
            $paymentId = $sessionData['payment_id'] ?? null;
            
            if (!$paymentId) {
                error_log("Service Fulfillment Error: Payment ID not found in session");
                $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'failed', [
                    'reason' => 'Payment record not found in session'
                ]);
                
                http_response_code(200);
                echo json_encode(['status' => 'acknowledged', 'message' => 'Payment not found']);
                return;
            }
            
            // Update payment record with Hubtel OrderId and payment details
            $amountPaid = $paymentInfo['AmountPaid'] ?? $subtotal;
            $amountAfterCharges = $paymentInfo['AmountAfterCharges'] ?? $amountPaid;
            $paymentType = $paymentInfo['PaymentType'] ?? 'mobilemoney';
            $paymentDescription = $paymentInfo['PaymentDescription'] ?? '';
            
            $this->paymentModel->update($paymentId, [
                'status' => 'success',
                'gateway_reference' => $orderId,
                'paid_at' => date('Y-m-d H:i:s'),
                'amount' => $amountAfterCharges // Update with actual amount after charges
            ]);
            
            error_log("Payment updated - ID: $paymentId, OrderId: $orderId, Amount: $amountAfterCharges");
            
            // Generate tickets
            require_once '../app/services/TicketGeneratorService.php';
            require_once '../app/services/RevenueAllocationService.php';
            
            $ticketService = new \App\Services\TicketGeneratorService();
            $revenueService = new \App\Services\RevenueAllocationService();
            
            // Use original amount from session since merchant absorbs fees
            // Customer paid the full amount, but Hubtel deducted fees from merchant
            $expectedAmount = $sessionData['total_amount'] ?? 0;
            $actualQuantity = $sessionData['quantity'] ?? 1;
            
            error_log("Ticket generation - Expected: $expectedAmount, Received: $amountAfterCharges, Quantity: $actualQuantity");
            
            // Prepare payment data for services
            // Use expectedAmount (what customer paid) not amountAfterCharges (what we received)
            $paymentData = [
                'payment_id' => $paymentId,
                'player_id' => $sessionData['player_id'] ?? null,
                'campaign_id' => $sessionData['campaign_id'],
                'station_id' => $sessionData['station_id'],
                'programme_id' => $sessionData['programme_id'] ?? null,
                'amount' => $expectedAmount,  // Use full amount customer paid, not amount after fees
                'quantity' => $actualQuantity
            ];
            
            // Generate tickets
            $ticketResult = $ticketService->generateTickets($paymentData);
            
            if (!$ticketResult || !isset($ticketResult['success']) || !$ticketResult['success']) {
                error_log("Service Fulfillment Error: Ticket generation failed");
                $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'failed', [
                    'reason' => 'Ticket generation failed'
                ]);
                
                http_response_code(200);
                echo json_encode(['status' => 'acknowledged', 'message' => 'Ticket generation failed']);
                return;
            }
            
            // Allocate revenue
            $revenueService->allocate($paymentData);
            
            error_log("Service Fulfillment SUCCESS - Payment: $paymentId, Tickets generated");
            
            // Send SMS notification
            try {
                require_once '../app/services/SMS/HubtelSmsService.php';
                $smsService = new \App\Services\SMS\HubtelSmsService();
                
                // Get campaign details
                $campaign = $this->campaignModel->findById($sessionData['campaign_id']);
                
                // Get player phone number
                $playerPhone = $this->cleanPhoneNumber($customerMobile);
                
                // Get ticket codes from result
                $tickets = $ticketResult['tickets'] ?? [];
                
                error_log("=== SENDING SMS ===");
                error_log("Phone: $playerPhone");
                error_log("Tickets: " . count($tickets));
                error_log("Campaign: " . ($campaign->name ?? $sessionData['campaign_name']));
                error_log("Amount: $expectedAmount");
                
                if (empty($tickets)) {
                    error_log("WARNING: No tickets found in result, cannot send SMS");
                } else {
                    $smsResult = $smsService->sendTicketConfirmation(
                        $playerPhone,
                        $tickets,
                        $campaign->name ?? $sessionData['campaign_name'],
                        $expectedAmount  // Show what customer paid, not what we received after fees
                    );
                    
                    if ($smsResult['success']) {
                        error_log("SMS sent successfully - MessageId: " . ($smsResult['message_id'] ?? 'N/A'));
                    } else {
                        error_log("SMS sending failed: " . ($smsResult['error'] ?? 'Unknown error'));
                        error_log("SMS Response: " . ($smsResult['response'] ?? 'No response'));
                    }
                }
            } catch (\Exception $smsError) {
                error_log("SMS Exception: " . $smsError->getMessage());
                error_log("SMS Stack trace: " . $smsError->getTraceAsString());
                // Don't fail the whole process if SMS fails
            }
            
            // Send success callback to Hubtel
            $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'success', [
                'payment_id' => $paymentId,
                'tickets_generated' => $ticketResult['ticket_count'] ?? $sessionData['quantity']
            ]);
            
            // Close session
            $this->sessionService->closeSession($sessionId);
            
            // Return success response
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Service fulfilled successfully',
                'payment_id' => $paymentId,
                'order_id' => $orderId
            ]);
            
        } catch (\Exception $e) {
            error_log("Service Fulfillment Exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Try to send failure callback if we have session and order IDs
            if (isset($sessionId) && isset($orderId)) {
                $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'failed', [
                    'reason' => 'Exception: ' . $e->getMessage()
                ]);
            }
            
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Fulfillment processing failed: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Send Service Fulfillment Callback to Hubtel
     * This notifies Hubtel whether service was successfully delivered
     * 
     * According to API spec:
     * - Endpoint: https://gs-callback.hubtel.com/callback
     * - Method: POST
     * - Must be sent within 1 hour of receiving fulfillment
     * - Requires IP whitelisting
     */
    private function sendServiceFulfillmentCallback($sessionId, $orderId, $serviceStatus, $metadata = null)
    {
        $callbackUrl = 'https://gs-callback.hubtel.com/callback';
        
        $payload = [
            'SessionId' => $sessionId,
            'OrderId' => $orderId,
            'ServiceStatus' => $serviceStatus, // 'success' or 'failed'
            'MetaData' => $metadata
        ];
        
        error_log("=== SENDING SERVICE FULFILLMENT CALLBACK ===" . PHP_EOL . json_encode($payload, JSON_PRETTY_PRINT));
        
        try {
            $ch = curl_init($callbackUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Cache-Control: no-cache'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                error_log("Service Fulfillment Callback Error: $error");
                return false;
            }
            
            error_log("Service Fulfillment Callback Response - HTTP $httpCode: $response");
            return $httpCode >= 200 && $httpCode < 300;
            
        } catch (\Exception $e) {
            error_log("Service Fulfillment Callback Exception: " . $e->getMessage());
            return false;
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
