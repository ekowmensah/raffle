<?php 
// Get draw ID from URL
$drawId = $data['draw_id'] ?? null;
$draw = $data['draw'] ?? null;
$campaign = $data['campaign'] ?? null;

if (!$draw || !$campaign) {
    echo "Draw not found";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Live Draw - <?= htmlspecialchars($campaign->name) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #020617, #0f172a, #1e293b);
            --accent: #22c55e;
            --accent-soft: rgba(34, 197, 94, 0.12);
            --danger: #ef4444;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --card-bg: rgba(15, 23, 42, 0.9);
            --border-soft: rgba(148, 163, 184, 0.3);
            --shadow-soft: 0 24px 70px rgba(15, 23, 42, 0.9);
            --winner-glow: 0 0 25px rgba(34, 197, 94, 0.8);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", system-ui, sans-serif;
            background: radial-gradient(circle at top, #1e293b 0, #020617 35%, #000 100%);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            display: grid;
            gap: 2rem;
            grid-template-columns: 1fr 380px;
        }

        .main-section {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(20px);
        }

        .header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .header h1 {
            margin: 0 0 0.5rem;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .status-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: var(--accent-soft);
            border: 1px solid var(--accent);
            border-radius: 1rem;
            margin-bottom: 2rem;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
        }

        .pulse-dot {
            width: 12px;
            height: 12px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .display-area {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.05), rgba(16, 185, 129, 0.05));
            border: 2px dashed var(--border-soft);
            border-radius: 1.5rem;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .rolling-number {
            font-size: 6rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--accent);
            text-shadow: var(--winner-glow);
            font-variant-numeric: tabular-nums;
            transition: all 0.1s ease;
        }

        .winner-display {
            margin-top: 2rem;
        }

        .winner-display h2 {
            font-size: 1.5rem;
            margin: 0 0 1rem;
            color: var(--accent);
        }

        .winner-info {
            font-size: 1.2rem;
            color: var(--text-main);
        }

        .controls {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-primary {
            background: var(--accent);
            color: #000;
        }

        .btn-primary:hover {
            background: #16a34a;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(34, 197, 94, 0.4);
        }

        .btn-secondary {
            background: rgba(148, 163, 184, 0.2);
            color: var(--text-main);
            border: 1px solid var(--border-soft);
        }

        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.3);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-card {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 1rem;
            padding: 1.5rem;
            backdrop-filter: blur(20px);
        }

        .info-card h3 {
            margin: 0 0 1rem;
            font-size: 1.2rem;
            color: var(--accent);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-soft);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-muted);
        }

        .info-value {
            font-weight: 600;
        }

        .winners-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .winner-item {
            padding: 1rem;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid var(--accent);
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .winner-rank {
            font-size: 0.9rem;
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .winner-ticket {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .winner-prize {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-section">
            <div class="card">
                <div class="header">
                    <h1><?= htmlspecialchars($campaign->name) ?></h1>
                    <p><?= ucfirst($draw->draw_type) ?> Draw - <?= date('F j, Y', strtotime($draw->draw_date)) ?></p>
                </div>

                <div class="status-bar">
                    <div class="status-indicator">
                        <div class="pulse-dot"></div>
                        <span id="status-text">Ready to Draw</span>
                    </div>
                    <div id="timer">00:30</div>
                </div>

                <div class="display-area">
                    <div id="rolling-display">
                        <div class="rolling-number" id="rolling-number">---</div>
                    </div>
                    <div id="winner-display" class="winner-display" style="display: none;">
                        <h2>🎉 Winner Selected!</h2>
                        <div class="winner-info">
                            <div>Phone: <strong id="winner-phone">---</strong></div>
                            <div>Ticket: <strong id="winner-ticket">---</strong></div>
                            <div>Prize: <strong id="winner-prize">---</strong></div>
                        </div>
                    </div>
                </div>

                <div class="controls">
                    <button class="btn btn-primary" id="start-btn" onclick="startDraw()">
                        <i class="fas fa-play"></i> Start Draw
                    </button>
                    <button class="btn btn-secondary" id="reset-btn" onclick="resetDraw()" disabled>
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='<?= url('draw/show/' . $draw->id) ?>'">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> Draw Information</h3>
                <div class="info-row">
                    <span class="info-label">Draw ID</span>
                    <span class="info-value">#<?= $draw->id ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Type</span>
                    <span class="info-value"><?= ucfirst($draw->draw_type) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Winners</span>
                    <span class="info-value"><?= $draw->winner_count ?? 3 ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Prize Pool</span>
                    <span class="info-value">GHS <?= number_format($draw->total_prize_pool, 2) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value"><?= ucfirst($draw->status) ?></span>
                </div>
            </div>

            <div class="info-card">
                <h3><i class="fas fa-trophy"></i> Winners</h3>
                <div class="winners-list" id="winners-list">
                    <p class="info-label" style="text-align: center;">No winners selected yet</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const drawId = <?= $draw->id ?>;
        const winnerCount = <?= $draw->winner_count ?? 3 ?>;
        let isDrawing = false;
        let currentWinnerIndex = 0;
        let winners = [];

        async function startDraw() {
            if (isDrawing) return;
            
            isDrawing = true;
            document.getElementById('start-btn').disabled = true;
            document.getElementById('status-text').textContent = 'Drawing in progress...';
            
            // Start rolling animation
            startRolling();
            
            try {
                // Call backend to conduct draw
                const response = await fetch('<?= url('draw/conduct/' . $draw->id) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'csrf_token=<?= csrf_token() ?>'
                });
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned HTML instead of JSON. Check error logs.');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    // Simulate rolling for 3 seconds
                    setTimeout(() => {
                        stopRolling();
                        displayWinners(result.winners);
                    }, 3000);
                } else {
                    alert('Error: ' + result.message);
                    resetDraw();
                }
            } catch (error) {
                console.error('Draw error:', error);
                alert('Failed to conduct draw. Please try again.');
                resetDraw();
            }
        }

        function startRolling() {
            const rollingNumber = document.getElementById('rolling-number');
            let interval = setInterval(() => {
                // Generate random ticket number
                const randomNum = Math.floor(Math.random() * 999999).toString().padStart(6, '0');
                rollingNumber.textContent = randomNum;
            }, 50);
            
            rollingNumber.dataset.interval = interval;
        }

        function stopRolling() {
            const rollingNumber = document.getElementById('rolling-number');
            clearInterval(parseInt(rollingNumber.dataset.interval));
        }

        function displayWinners(winnersData) {
            winners = winnersData;
            const winnersList = document.getElementById('winners-list');
            winnersList.innerHTML = '';
            
            winnersData.forEach((winner, index) => {
                const item = document.createElement('div');
                item.className = 'winner-item';
                item.innerHTML = `
                    <div class="winner-rank">${getRankName(winner.prize_rank)}</div>
                    <div class="winner-ticket">${winner.player_phone}</div>
                    <div class="winner-ticket">${winner.ticket_code}</div>
                    <div class="winner-prize">GHS ${parseFloat(winner.prize_amount).toFixed(2)}</div>
                `;
                winnersList.appendChild(item);
                
                // Show first winner in main display
                if (index === 0) {
                    document.getElementById('rolling-number').textContent = winner.ticket_code;
                    document.getElementById('winner-phone').textContent = winner.player_phone;
                    document.getElementById('winner-ticket').textContent = winner.ticket_code;
                    document.getElementById('winner-prize').textContent = 'GHS ' + parseFloat(winner.prize_amount).toFixed(2);
                    document.getElementById('winner-display').style.display = 'block';
                }
            });
            
            document.getElementById('status-text').textContent = 'Draw Completed!';
            document.getElementById('reset-btn').disabled = false;
            isDrawing = false;
        }

        function getRankName(rank) {
            const names = {
                1: '🥇 1st Prize',
                2: '🥈 2nd Prize',
                3: '🥉 3rd Prize'
            };
            return names[rank] || `${rank}th Prize`;
        }

        function resetDraw() {
            document.getElementById('rolling-number').textContent = '---';
            document.getElementById('winner-phone').textContent = '---';
            document.getElementById('winner-ticket').textContent = '---';
            document.getElementById('winner-prize').textContent = '---';
            document.getElementById('winner-display').style.display = 'none';
            document.getElementById('winners-list').innerHTML = '<p class="info-label" style="text-align: center;">No winners selected yet</p>';
            document.getElementById('status-text').textContent = 'Ready to Draw';
            document.getElementById('start-btn').disabled = false;
            document.getElementById('reset-btn').disabled = true;
            isDrawing = false;
            winners = [];
        }
    </script>
</body>
</html>
