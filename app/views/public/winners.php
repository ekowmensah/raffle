<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recent Winners | eTickets Raffle</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 3rem;
        }

        .navbar-custom {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-custom .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #667eea;
        }

        .hero-section {
            text-align: center;
            padding: 3rem 2rem 2rem;
            color: white;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        .stats-bar {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-card .stat-label {
            color: #6b7280;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .winners-grid {
            display: grid;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .winner-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .winner-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .winner-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        }

        .winner-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .rank-badge {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
        .rank-3 { background: linear-gradient(135deg, #fb923c, #f97316); }
        .rank-other { background: linear-gradient(135deg, #667eea, #764ba2); }

        .winner-info {
            flex: 1;
        }

        .campaign-name {
            font-size: 0.85rem;
            color: #667eea;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .draw-date {
            font-size: 0.9rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .winner-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            border-radius: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .detail-value {
            font-size: 1.1rem;
            color: #111827;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }

        .prize-amount {
            font-size: 1.8rem !important;
            color: #10b981 !important;
            font-weight: 800 !important;
        }

        .phone-masked {
            color: #667eea;
        }

        .ticket-code {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        .no-winners {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            margin-top: 2rem;
        }

        .no-winners i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .no-winners h3 {
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .no-winners p {
            color: #9ca3af;
        }

        .cta-section {
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 3rem;
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .cta-section h3 {
            font-size: 2rem;
            color: #111827;
            margin-bottom: 1rem;
        }

        .cta-section p {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .btn-play {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-play:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
            color: white;
        }

        .station-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #eff6ff;
            color: #1e40af;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .stats-bar {
                grid-template-columns: 1fr;
            }

            .winner-header {
                flex-direction: column;
                text-align: center;
            }

            .winner-details {
                grid-template-columns: 1fr;
            }

            .nav-links {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="<?= url('public') ?>">
            <i class="fas fa-ticket"></i>
            <strong>eTickets Raffle</strong>
        </a>
        <ul class="nav-links">
            <li><a href="<?= url('public') ?>">Home</a></li>
            <li><a href="<?= url('public/howToPlay') ?>">How to Play</a></li>
            <li><a href="<?= url('public/winners') ?>" class="active">Winners</a></li>
        </ul>
    </div>
</nav>

<div class="hero-section">
    <h1><i class="fas fa-trophy"></i> Recent Winners</h1>
    <p>Celebrating our lucky winners!</p>
</div>

<div class="stats-bar">
    <div class="stat-card">
        <i class="fas fa-trophy" style="color: #fbbf24;"></i>
        <div class="stat-value"><?= number_format($total_winners ?? 0) ?></div>
        <div class="stat-label">Total Winners</div>
    </div>
    <div class="stat-card">
        <i class="fas fa-coins" style="color: #10b981;"></i>
        <div class="stat-value">GHS <?= number_format($total_prizes ?? 0, 2) ?></div>
        <div class="stat-label">Total Prizes Awarded</div>
    </div>
    <div class="stat-card">
        <i class="fas fa-bullhorn" style="color: #667eea;"></i>
        <div class="stat-value"><?= $active_campaigns ?? 0 ?></div>
        <div class="stat-label">Active Campaigns</div>
    </div>
</div>

<div class="container">
    <?php if (empty($winners)): ?>
        <div class="no-winners">
            <i class="fas fa-trophy"></i>
            <h3>No Winners Yet</h3>
            <p>Winners will be displayed here once draws are conducted</p>
        </div>
    <?php else: ?>
        <div class="winners-grid">
            <?php 
            function maskPhoneNumber($phone) {
                // Mask middle digits: +233 24 XXX 4567 -> +233 24 *** 4567
                if (strlen($phone) >= 10) {
                    $start = substr($phone, 0, 7);
                    $end = substr($phone, -4);
                    return $start . ' *** ' . $end;
                }
                return $phone;
            }
            
            foreach ($winners as $winner): 
            ?>
            <div class="winner-card">
                <div class="winner-header">
                    <div class="rank-badge rank-<?= $winner->prize_rank <= 3 ? $winner->prize_rank : 'other' ?>">
                        <?php if ($winner->prize_rank == 1): ?>
                            <i class="fas fa-crown"></i>
                        <?php else: ?>
                            #<?= $winner->prize_rank ?>
                        <?php endif; ?>
                    </div>
                    <div class="winner-info">
                        <div class="campaign-name">
                            <?= htmlspecialchars($winner->campaign_name) ?>
                            <?php if ($winner->station_name): ?>
                                <span class="station-badge">
                                    <i class="fas fa-broadcast-tower"></i> <?= htmlspecialchars($winner->station_name) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="draw-date">
                            <i class="fas fa-calendar"></i>
                            <?= date('F j, Y', strtotime($winner->draw_date)) ?>
                            <span style="margin-left: 1rem;">
                                <i class="fas fa-tag"></i> <?= strtoupper($winner->draw_type) ?> Draw
                            </span>
                        </div>
                    </div>
                </div>

                <div class="winner-details">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-phone"></i> Phone Number</div>
                        <div class="detail-value phone-masked"><?= maskPhoneNumber($winner->phone_number) ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-ticket"></i> Ticket Code</div>
                        <div class="detail-value ticket-code"><?= htmlspecialchars($winner->ticket_code) ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-trophy"></i> Prize Won</div>
                        <div class="detail-value prize-amount">GHS <?= number_format($winner->prize_amount, 2) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="cta-section">
        <h3>🎉 Want to be our next winner?</h3>
        <p>Join our active campaigns and stand a chance to win amazing prizes!</p>
        <a href="<?= url('public') ?>" class="btn-play">
            <i class="fas fa-play-circle"></i> Play Now
        </a>
    </div>
</div>

</body>
</html>
