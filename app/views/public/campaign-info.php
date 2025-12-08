<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= htmlspecialchars($campaign->name) ?> | eTickets Raffle</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #0f0f23;
            min-height: 100vh;
            color: #fff;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            opacity: 0.1;
            z-index: -1;
        }
        
        /* Navbar */
        .navbar-custom {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid rgba(102, 126, 234, 0.3);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #f093fb !important;
        }
        
        /* Campaign Hero */
        .campaign-hero {
            background: rgba(15, 15, 35, 0.8);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 25px;
            margin: 100px auto 40px;
            max-width: 1200px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        
        .campaign-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }
        
        @media (max-width: 768px) {
            .campaign-hero {
                margin: 80px 1rem 2rem;
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
        }
        
        h1 {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }
        }
        
        .campaign-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .badge-active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        
        .campaign-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #f093fb;
            box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #f093fb;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Countdown Timer */
        .countdown-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(240, 147, 251, 0.2));
            border: 2px solid rgba(240, 147, 251, 0.5);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }
        
        .countdown-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f093fb;
            margin-bottom: 1.5rem;
        }
        
        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .countdown-item {
            background: rgba(15, 15, 35, 0.8);
            border: 2px solid rgba(102, 126, 234, 0.5);
            border-radius: 15px;
            padding: 1rem 1.5rem;
            min-width: 80px;
        }
        
        .countdown-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            display: block;
        }
        
        .countdown-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2));
            border: 3px solid rgba(16, 185, 129, 0.5);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 2rem 0;
            text-align: center;
        }
        
        .cta-title {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1rem;
        }
        
        .cta-subtitle {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 2rem;
        }
        
        .btn-buy-tickets {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 1.2rem 3rem;
            font-size: 1.3rem;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-buy-tickets:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.6);
            color: white;
            text-decoration: none;
        }
        
        .btn-buy-tickets i {
            margin-right: 0.5rem;
        }
        
        /* Info Cards */
        .info-section {
            margin: 3rem 0;
        }
        
        .info-card {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .info-card h3 {
            color: #f093fb;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .info-card p {
            color: rgba(255,255,255,0.9);
            line-height: 1.8;
            margin-bottom: 0;
        }
        
        .info-list {
            list-style: none;
            padding: 0;
        }
        
        .info-list li {
            padding: 0.5rem 0;
            color: rgba(255,255,255,0.9);
        }
        
        .info-list li i {
            color: #10b981;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= url('public') ?>">
            <i class="fas fa-ticket"></i>
            eTickets Raffle
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" style="border-color: rgba(255,255,255,0.3);">
            <span class="navbar-toggler-icon" style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e\");"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('public') ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('public/winners') ?>">
                        <i class="fas fa-trophy"></i> Winners
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="campaign-hero">
        <span class="campaign-badge badge-active">
            <i class="fas fa-circle-notch fa-spin"></i> Active Campaign
        </span>
        
        <h1><?= htmlspecialchars($campaign->name) ?></h1>
        
        <?php if ($campaign->campaign_type === 'item'): ?>
        <!-- Item Showcase -->
        <div class="item-showcase" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1)); border: 3px solid rgba(16, 185, 129, 0.4); border-radius: 20px; padding: 2rem; margin: 2rem 0; text-align: center;">
            <?php if ($campaign->item_image): ?>
            <div style="margin-bottom: 1.5rem;">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($campaign->item_image) ?>" alt="<?= htmlspecialchars($campaign->item_name) ?>" style="max-width: 100%; max-height: 400px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
            </div>
            <?php endif; ?>
            <h2 style="color: #10b981; font-size: 2rem; font-weight: 800; margin-bottom: 1rem;">
                <i class="fas fa-gift"></i> WIN: <?= htmlspecialchars($campaign->item_name) ?>
            </h2>
            <div style="font-size: 1.5rem; color: #f093fb; font-weight: 700; margin-bottom: 1rem;">
                Worth GHS <?= number_format($campaign->item_value, 0) ?>
            </div>
            <?php if (!empty($campaign->item_description)): ?>
            <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; line-height: 1.7; max-width: 800px; margin: 0 auto;">
                <?= nl2br(htmlspecialchars($campaign->item_description)) ?>
            </p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <?php if (!empty($campaign->description)): ?>
        <p class="campaign-description">
            <?= nl2br(htmlspecialchars($campaign->description)) ?>
        </p>
        <?php endif; ?>
        <?php endif; ?>
        
        <!-- Social Proof & Urgency Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="stat-value"><?= $stats->total_players ?? 0 ?>+</div>
                <div class="stat-label">Players Competing</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <?php 
                    $totalRevenue = ($stats->total_tickets ?? 0) * $campaign->ticket_price;
                    $prizePool = $totalRevenue * ($campaign->prize_pool_percent / 100);
                ?>
                <div class="stat-value"><?= $campaign->currency ?> <?= number_format($prizePool, 0) ?></div>
                <div class="stat-label">Total Prize Money</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="stat-value"><?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?></div>
                <div class="stat-label">Only Per Ticket!</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <?php 
                    $ticketsSold = $stats->total_tickets ?? 0;
                    $recentActivity = min(100, max(15, round($ticketsSold / 10)));
                ?>
                <div class="stat-value"><?= $recentActivity ?>+</div>
                <div class="stat-label">Sold Today</div>
            </div>
        </div>
        
        <!-- Countdown Timer -->
        <div class="countdown-section">
            <div class="countdown-title">
                <i class="fas fa-clock"></i> Campaign Ends In:
            </div>
            <div class="countdown-timer" id="countdown">
                <div class="countdown-item">
                    <span class="countdown-value" id="days">00</span>
                    <span class="countdown-label">Days</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="hours">00</span>
                    <span class="countdown-label">Hours</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="minutes">00</span>
                    <span class="countdown-label">Minutes</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="seconds">00</span>
                    <span class="countdown-label">Seconds</span>
                </div>
            </div>
        </div>
        
        <!-- Urgency Banner -->
        <?php 
            $endDate = new DateTime($campaign->end_date);
            $now = new DateTime();
            $interval = $now->diff($endDate);
            $daysLeft = max(0, $interval->days);
        ?>
        <?php if ($daysLeft <= 7 && $daysLeft > 0): ?>
        <div class="urgency-banner" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2)); border: 2px solid rgba(239, 68, 68, 0.5); border-radius: 15px; padding: 1.5rem; margin: 2rem 0; text-align: center;">
            <h3 style="color: #ef4444; font-size: 1.3rem; margin-bottom: 0.5rem;">
                <i class="fas fa-exclamation-triangle"></i> HURRY! Only <?= $daysLeft ?> Day<?= $daysLeft != 1 ? 's' : '' ?> Left!
            </h3>
            <p style="color: rgba(255,255,255,0.9); margin: 0;">Don't miss your chance to win! Campaign ends soon.</p>
        </div>
        <?php endif; ?>
        
        <!-- CTA Section -->
        <div class="cta-section">
            <div class="cta-title">
                <i class="fas fa-gem"></i> Your Winning Moment Starts Here!
            </div>
            <div class="cta-subtitle">
                <?php if ($prizePool > 0): ?>
                    Join <?= $stats->total_players ?? 0 ?>+ players competing for GHS <?= number_format($prizePool, 0) ?> in prizes!
                <?php else: ?>
                    Be among the first to enter and increase your winning chances!
                <?php endif; ?>
            </div>
            <a href="<?= url('public/buyTicket?campaign=' . $campaign->id) ?>" class="btn-buy-tickets">
                <i class="fas fa-ticket-alt"></i> Get My Winning Tickets
            </a>
            <div style="margin-top: 1rem; font-size: 0.9rem; color: rgba(255,255,255,0.7);">
                <i class="fas fa-shield-alt"></i> Secure Payment • <i class="fas fa-sms"></i> Instant SMS Confirmation
            </div>
        </div>
        
        <!-- Info Section -->
        <div class="info-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-info-circle"></i> Campaign Details</h3>
                        <ul class="info-list">
                            <li><i class="fas fa-calendar-alt"></i> <strong>Starts:</strong> <?= formatDate($campaign->start_date, 'M d, Y') ?></li>
                            <li><i class="fas fa-calendar-check"></i> <strong>Ends:</strong> <?= formatDate($campaign->end_date, 'M d, Y') ?></li>
                            <?php if ($campaign->daily_draw_enabled): ?>
                            <li><i class="fas fa-sync-alt"></i> <strong>Daily Draws:</strong> Enabled</li>
                            <?php endif; ?>
                            <li><i class="fas fa-broadcast-tower"></i> <strong>Station:</strong> <?= htmlspecialchars($campaign->station_name ?? 'N/A') ?></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-gift"></i> What You Could Win</h3>
                        <ul class="info-list">
                            <?php if ($campaign->campaign_type === 'item'): ?>
                                <!-- Item Campaign Prizes -->
                                <?php if ($campaign->winner_selection_type === 'single'): ?>
                                    <li><i class="fas fa-trophy" style="color: #ffd700;"></i> <strong>Grand Prize:</strong> <?= htmlspecialchars($campaign->item_name) ?></li>
                                    <li><i class="fas fa-tag"></i> <strong>Value:</strong> GHS <?= number_format($campaign->item_value, 0) ?></li>
                                    <li><i class="fas fa-user"></i> <strong>Winners:</strong> 1 Lucky Player!</li>
                                <?php elseif ($campaign->winner_selection_type === 'multiple'): ?>
                                    <li><i class="fas fa-trophy" style="color: #ffd700;"></i> <strong>Prize:</strong> <?= htmlspecialchars($campaign->item_name) ?></li>
                                    <li><i class="fas fa-tag"></i> <strong>Value:</strong> GHS <?= number_format($campaign->item_value, 0) ?> each</li>
                                    <li><i class="fas fa-users"></i> <strong>Winners:</strong> <?= $campaign->item_quantity ?> Lucky Players!</li>
                                <?php elseif ($campaign->winner_selection_type === 'tiered'): ?>
                                    <?php 
                                        $campaignModel = new \App\Models\Campaign();
                                        $itemPrizes = $campaignModel->getItemPrizes($campaign->id);
                                        $icons = ['fa-crown' => '#ffd700', 'fa-medal' => '#c0c0c0', 'fa-award' => '#cd7f32'];
                                        $iconKeys = array_keys($icons);
                                    ?>
                                    <?php foreach ($itemPrizes as $index => $prize): ?>
                                        <?php 
                                            $icon = $iconKeys[$index] ?? 'fa-gift';
                                            $color = $icons[$icon] ?? '#10b981';
                                        ?>
                                        <li>
                                            <i class="fas <?= $icon ?>" style="color: <?= $color ?>;"></i> 
                                            <strong><?= $index + 1 ?><?= $index == 0 ? 'st' : ($index == 1 ? 'nd' : ($index == 2 ? 'rd' : 'th')) ?> Prize:</strong> 
                                            <?php if ($prize->prize_type === 'item'): ?>
                                                <?= htmlspecialchars($prize->item_name) ?> (GHS <?= number_format($prize->item_value, 0) ?>)
                                            <?php else: ?>
                                                GHS <?= number_format($prize->cash_amount, 0) ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <li><i class="fas fa-star"></i> <strong>100% Guaranteed:</strong> Item will be awarded!</li>
                            <?php else: ?>
                                <!-- Cash Campaign Prizes -->
                                <?php 
                                    // Calculate prize tiers based on prize pool percentage
                                    $firstPrize = $prizePool * 0.50; // 50% to 1st
                                    $secondPrize = $prizePool * 0.30; // 30% to 2nd
                                    $thirdPrize = $prizePool * 0.20; // 20% to 3rd
                                ?>
                                <?php if ($prizePool > 0): ?>
                                <li><i class="fas fa-crown" style="color: #ffd700;"></i> <strong>1st Prize:</strong> GHS <?= number_format($firstPrize, 0) ?> 🎉</li>
                                <li><i class="fas fa-medal" style="color: #c0c0c0;"></i> <strong>2nd Prize:</strong> GHS <?= number_format($secondPrize, 0) ?> 🎊</li>
                                <li><i class="fas fa-award" style="color: #cd7f32;"></i> <strong>3rd Prize:</strong> GHS <?= number_format($thirdPrize, 0) ?> 🎁</li>
                                <?php else: ?>
                                <li><i class="fas fa-trophy"></i> <strong>Prize Pool:</strong> <?= $campaign->prize_pool_percent ?>% of total revenue</li>
                                <li><i class="fas fa-star"></i> <strong>Multiple Winners:</strong> More tickets sold = Bigger prizes!</li>
                                <?php endif; ?>
                                <li><i class="fas fa-percentage"></i> <strong><?= $campaign->prize_pool_percent ?>%</strong> goes directly to winners!</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="info-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15)); border-color: rgba(16, 185, 129, 0.4);">
                <h3><i class="fas fa-rocket"></i> Why Players Love This Campaign</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div style="text-align: center; padding: 1rem;">
                            <i class="fas fa-bolt" style="font-size: 2.5rem; color: #10b981; margin-bottom: 0.5rem;"></i>
                            <h5 style="color: #fff; margin-bottom: 0.5rem;">Instant Entry</h5>
                            <p style="font-size: 0.9rem;">Buy tickets and get SMS confirmation in seconds!</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="text-align: center; padding: 1rem;">
                            <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #10b981; margin-bottom: 0.5rem;"></i>
                            <h5 style="color: #fff; margin-bottom: 0.5rem;">100% Fair</h5>
                            <p style="font-size: 0.9rem;">Random selection ensures everyone has equal chances!</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="text-align: center; padding: 1rem;">
                            <i class="fas fa-money-bill-wave" style="font-size: 2.5rem; color: #10b981; margin-bottom: 0.5rem;"></i>
                            <h5 style="color: #fff; margin-bottom: 0.5rem;">Big Prizes</h5>
                            <p style="font-size: 0.9rem;">The more you play, the more you could win!</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <h3><i class="fas fa-check-circle"></i> Simple 3-Step Process</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div style="padding: 1rem; text-align: center;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; font-weight: 800;">1</div>
                            <h5 style="color: #f093fb;">Buy Tickets</h5>
                            <p style="font-size: 0.9rem;">Choose your campaign and quantity</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="padding: 1rem; text-align: center;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; font-weight: 800;">2</div>
                            <h5 style="color: #f093fb;">Get Your Codes</h5>
                            <p style="font-size: 0.9rem;">Receive unique ticket codes via SMS</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="padding: 1rem; text-align: center;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; font-weight: 800;">3</div>
                            <h5 style="color: #10b981;">Win Big!</h5>
                            <p style="font-size: 0.9rem;">Wait for the draw and claim your prize</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>
// Countdown Timer
const endDate = new Date('<?= $campaign->end_date ?>').getTime();

function updateCountdown() {
    const now = new Date().getTime();
    const distance = endDate - now;
    
    if (distance < 0) {
        document.getElementById('countdown').innerHTML = '<div class="countdown-item"><span class="countdown-value">ENDED</span></div>';
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById('days').textContent = String(days).padStart(2, '0');
    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
}

// Update countdown every second
updateCountdown();
setInterval(updateCountdown, 1000);
</script>

</body>
</html>
