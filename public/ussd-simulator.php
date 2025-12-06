<?php
/**
 * USSD Simulator for Testing - Enhanced Version
 * Access via: http://localhost/raffle/public/ussd-simulator.php
 */

session_start();

// Initialize session data
if (!isset($_SESSION['ussd_session'])) {
    $_SESSION['ussd_session'] = [
        'sessionId' => 'SIM' . time() . rand(1000, 9999),
        'phoneNumber' => '233241234567',
        'text' => '',
        'history' => [],
        'startTime' => time()
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        // Save history before reset
        $oldHistory = $_SESSION['ussd_session']['history'] ?? [];
        unset($_SESSION['ussd_session']);
        unset($_SESSION['last_response']);
        unset($_SESSION['session_ended']);
        unset($_SESSION['debug_info']);
        header('Location: ussd-simulator.php');
        exit;
    }
    
    if (isset($_POST['back'])) {
        // Go back one step
        $textParts = explode('*', $_SESSION['ussd_session']['text']);
        if (count($textParts) > 0) {
            array_pop($textParts);
            $_SESSION['ussd_session']['text'] = implode('*', $textParts);
            
            // Make request with updated text
            $response = makeUssdRequest(
                $_SESSION['ussd_session']['sessionId'],
                $_SESSION['ussd_session']['phoneNumber'],
                $_SESSION['ussd_session']['text']
            );
            
            $_SESSION['last_response'] = $response['body'];
            $_SESSION['debug_info'] = $response['debug'];
            $_SESSION['session_ended'] = (strpos($response['body'], 'END') === 0);
        }
    }
    
    if (isset($_POST['input']) && $_POST['input'] !== '') {
        $input = $_POST['input'];
        
        // Append input to text
        if ($_SESSION['ussd_session']['text'] === '') {
            $_SESSION['ussd_session']['text'] = $input;
        } else {
            $_SESSION['ussd_session']['text'] .= '*' . $input;
        }
        
        // Make USSD request
        $response = makeUssdRequest(
            $_SESSION['ussd_session']['sessionId'],
            $_SESSION['ussd_session']['phoneNumber'],
            $_SESSION['ussd_session']['text']
        );
        
        // Store in history
        $_SESSION['ussd_session']['history'][] = [
            'input' => $input,
            'text' => $_SESSION['ussd_session']['text'],
            'response' => $response['body'],
            'timestamp' => time()
        ];
        
        $_SESSION['last_response'] = $response['body'];
        $_SESSION['debug_info'] = $response['debug'];
        
        // Check if session ended
        if (strpos($response['body'], 'END') === 0) {
            $_SESSION['session_ended'] = true;
        }
    }
    
    if (isset($_POST['phone'])) {
        $_SESSION['ussd_session']['phoneNumber'] = $_POST['phone'];
    }
}

// Function to make USSD request
function makeUssdRequest($sessionId, $phoneNumber, $text) {
    $url = 'http://localhost/raffle/public/index.php?url=ussd';
    
    $data = [
        'sessionId' => $sessionId,
        'phoneNumber' => $phoneNumber,
        'text' => $text
    ];
    
    $startTime = microtime(true);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $fullResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $responseTime = round((microtime(true) - $startTime) * 1000, 2);
    
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($fullResponse, 0, $headerSize);
    $body = substr($fullResponse, $headerSize);
    
    curl_close($ch);
    
    if ($httpCode != 200) {
        $body = "ERROR: HTTP {$httpCode}\n{$body}";
    }
    
    return [
        'body' => $body,
        'debug' => [
            'httpCode' => $httpCode,
            'responseTime' => $responseTime,
            'requestData' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ];
}

$lastResponse = $_SESSION['last_response'] ?? 'CON Welcome to Raffle System\n1. Buy Ticket\n2. Check My Tickets\n3. Check Winners\n0. Exit';
$sessionEnded = $_SESSION['session_ended'] ?? false;
$currentText = $_SESSION['ussd_session']['text'] ?? '';
$phoneNumber = $_SESSION['ussd_session']['phoneNumber'] ?? '233241234567';
$history = $_SESSION['ussd_session']['history'] ?? [];
$debugInfo = $_SESSION['debug_info'] ?? null;

// Extract payment ID from response if present
$paymentId = null;
if (preg_match('/PAY(\d+)/', $lastResponse, $matches)) {
    $paymentId = $matches[1];
}

// Calculate session duration
$sessionDuration = isset($_SESSION['ussd_session']['startTime']) ? 
    (time() - $_SESSION['ussd_session']['startTime']) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USSD Simulator - Raffle System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 500px 1fr;
            gap: 20px;
            align-items: start;
        }
        
        @media (max-width: 1200px) {
            .container {
                grid-template-columns: 1fr;
            }
            .debug-panel, .history-panel {
                order: 3;
            }
        }
        
        .phone-frame {
            background: #000;
            border-radius: 40px;
            padding: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .phone-screen {
            background: #fff;
            border-radius: 25px;
            overflow: hidden;
        }
        
        .phone-header {
            background: #2c3e50;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }
        
        .phone-display {
            background: #ecf0f1;
            min-height: 400px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        .phone-display.ended {
            background: #d5f4e6;
        }
        
        .phone-keypad {
            background: #34495e;
            padding: 20px;
        }
        
        .keypad-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .key {
            background: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .key:hover {
            background: #3498db;
            color: #fff;
            transform: scale(1.1);
        }
        
        .key:active {
            transform: scale(0.95);
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        
        .action-btn {
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .back-btn {
            background: #f39c12;
            color: #fff;
        }
        
        .back-btn:hover {
            background: #e67e22;
        }
        
        .back-btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .reset-btn {
            background: #e74c3c;
            color: #fff;
        }
        
        .reset-btn:hover {
            background: #c0392b;
        }
        
        .info-panel {
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .info-value {
            color: #7f8c8d;
            font-family: monospace;
        }
        
        .phone-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #3498db;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        h1 {
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-active {
            background: #27ae60;
            color: #fff;
        }
        
        .status-ended {
            background: #e74c3c;
            color: #fff;
        }
        
        .payment-action {
            background: #27ae60;
            color: #fff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            text-align: center;
        }
        
        .payment-action h4 {
            margin-bottom: 10px;
        }
        
        .complete-payment-btn {
            background: #fff;
            color: #27ae60;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .complete-payment-btn:hover {
            background: #f0f0f0;
            transform: scale(1.05);
        }
        
        .debug-panel, .history-panel {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 20px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .debug-panel h3, .history-panel h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .debug-item {
            background: #ecf0f1;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
        }
        
        .debug-label {
            font-weight: bold;
            color: #2c3e50;
            display: inline-block;
            min-width: 120px;
        }
        
        .debug-value {
            color: #7f8c8d;
        }
        
        .history-item {
            background: #f8f9fa;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .history-input {
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 5px;
        }
        
        .history-response {
            font-family: monospace;
            font-size: 12px;
            color: #7f8c8d;
            white-space: pre-wrap;
            margin-top: 5px;
            padding: 8px;
            background: #fff;
            border-radius: 4px;
        }
        
        .history-time {
            font-size: 11px;
            color: #95a5a6;
            margin-top: 5px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
            margin-bottom: 10px;
        }
        
        .quick-btn {
            padding: 8px;
            background: #ecf0f1;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .quick-btn:hover {
            background: #3498db;
            color: #fff;
            border-color: #3498db;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .stat-card {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .response-type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .response-con {
            background: #3498db;
            color: #fff;
        }
        
        .response-end {
            background: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center; color: #fff; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">📱 USSD Simulator - Enhanced</h1>
    
    <div class="container">
        <!-- Debug Panel (Left) -->
        <div class="debug-panel">
            <h3>🔍 Debug Info</h3>
            
            <?php if ($debugInfo): ?>
            <div class="debug-item">
                <span class="debug-label">HTTP Code:</span>
                <span class="debug-value"><?= $debugInfo['httpCode'] ?></span>
            </div>
            <div class="debug-item">
                <span class="debug-label">Response Time:</span>
                <span class="debug-value"><?= $debugInfo['responseTime'] ?>ms</span>
            </div>
            <div class="debug-item">
                <span class="debug-label">Timestamp:</span>
                <span class="debug-value"><?= $debugInfo['timestamp'] ?></span>
            </div>
            <?php endif; ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= count($history) ?></div>
                    <div class="stat-label">Steps</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $sessionDuration ?>s</div>
                    <div class="stat-label">Duration</div>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <h4 style="margin-bottom: 10px; color: #2c3e50;">Quick Actions</h4>
                <div class="quick-actions">
                    <button class="quick-btn" onclick="sendInput('1')">Buy Ticket</button>
                    <button class="quick-btn" onclick="sendInput('2')">My Tickets</button>
                    <button class="quick-btn" onclick="sendInput('3')">Winners</button>
                    <button class="quick-btn" onclick="sendInput('0')">Back/Exit</button>
                </div>
            </div>
        </div>
        
        <!-- Phone Simulator (Center) -->
        <div class="phone-frame">
            <div class="phone-screen">
                <div class="phone-header">
                    *123# - Raffle System
                    <span class="status-badge <?= $sessionEnded ? 'status-ended' : 'status-active' ?>">
                        <?= $sessionEnded ? 'ENDED' : 'ACTIVE' ?>
                    </span>
                </div>
                
                <div class="phone-display <?= $sessionEnded ? 'ended' : '' ?>">
                    <span class="response-type <?= strpos($lastResponse, 'CON') === 0 ? 'response-con' : 'response-end' ?>">
                        <?= strpos($lastResponse, 'CON') === 0 ? 'CONTINUE' : 'END SESSION' ?>
                    </span>
<?= htmlspecialchars($lastResponse) ?>
                </div>
                
                <div class="phone-keypad">
                    <form method="POST" id="ussdForm">
                        <input type="text" 
                               name="phone" 
                               class="phone-input" 
                               placeholder="Phone Number" 
                               value="<?= htmlspecialchars($phoneNumber) ?>"
                               pattern="[0-9]+"
                               title="Numbers only">
                        
                        <div class="keypad-row">
                            <button type="button" class="key" onclick="sendInput('1')">1</button>
                            <button type="button" class="key" onclick="sendInput('2')">2</button>
                            <button type="button" class="key" onclick="sendInput('3')">3</button>
                        </div>
                        <div class="keypad-row">
                            <button type="button" class="key" onclick="sendInput('4')">4</button>
                            <button type="button" class="key" onclick="sendInput('5')">5</button>
                            <button type="button" class="key" onclick="sendInput('6')">6</button>
                        </div>
                        <div class="keypad-row">
                            <button type="button" class="key" onclick="sendInput('7')">7</button>
                            <button type="button" class="key" onclick="sendInput('8')">8</button>
                            <button type="button" class="key" onclick="sendInput('9')">9</button>
                        </div>
                        <div class="keypad-row">
                            <button type="button" class="key" onclick="sendInput('*')">*</button>
                            <button type="button" class="key" onclick="sendInput('0')">0</button>
                            <button type="button" class="key" onclick="sendInput('#')">#</button>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" name="back" class="action-btn back-btn" <?= empty($currentText) ? 'disabled' : '' ?>>
                                ⬅️ Back
                            </button>
                            <button type="submit" name="reset" class="action-btn reset-btn">
                                🔄 Reset
                            </button>
                        </div>
                        
                        <input type="hidden" name="input" id="inputField">
                    </form>
                </div>
            </div>
        </div>
        
        <!-- History Panel (Right) -->
        <div class="history-panel">
            <h3>📜 Session History</h3>
            
            <div class="info-panel" style="margin-bottom: 15px;">
                <div class="info-row">
                    <span class="info-label">Session ID:</span>
                    <span class="info-value"><?= htmlspecialchars($_SESSION['ussd_session']['sessionId']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?= htmlspecialchars($phoneNumber) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Input Text:</span>
                    <span class="info-value"><?= htmlspecialchars($currentText ?: 'None') ?></span>
                </div>
            </div>
            
            <?php if ($paymentId && $sessionEnded): ?>
            <div class="payment-action" style="margin-bottom: 15px;">
                <h4>💳 Complete Payment</h4>
                <p style="margin-bottom: 15px;">Payment ID: <?= $paymentId ?></p>
                <form method="POST" action="http://localhost/raffle/public/index.php?url=payment/manual" target="_blank">
                    <input type="hidden" name="payment_id" value="<?= $paymentId ?>">
                    <button type="submit" class="complete-payment-btn">
                        ✅ Complete Payment & Generate Tickets
                    </button>
                </form>
                <p style="font-size: 12px; margin-top: 10px; opacity: 0.8;">
                    Opens in new tab
                </p>
            </div>
            <?php endif; ?>
            
            <div style="margin-top: 20px;">
                <h4 style="margin-bottom: 10px; color: #2c3e50;">Steps (<?= count($history) ?>)</h4>
                <?php if (empty($history)): ?>
                    <p style="color: #95a5a6; text-align: center; padding: 20px;">No steps yet. Start by pressing a key.</p>
                <?php else: ?>
                    <?php foreach (array_reverse($history) as $index => $item): ?>
                    <div class="history-item">
                        <div class="history-input">
                            Step <?= count($history) - $index ?>: Input "<?= htmlspecialchars($item['input']) ?>"
                        </div>
                        <div class="history-response"><?= htmlspecialchars($item['response']) ?></div>
                        <div class="history-time">
                            <?= date('H:i:s', $item['timestamp']) ?> (<?= $item['text'] ?>)
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function sendInput(value) {
            document.getElementById('inputField').value = value;
            document.getElementById('ussdForm').submit();
        }
        
        // Keyboard support
        document.addEventListener('keypress', function(e) {
            if (e.key >= '0' && e.key <= '9') {
                sendInput(e.key);
            } else if (e.key === '*' || e.key === '#') {
                sendInput(e.key);
            }
        });
    </script>
</body>
</html>
