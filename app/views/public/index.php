<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Play & Win Big' ?> | eTickets Raffle</title>
    
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
            color: #fff;
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            opacity: 0.1;
            z-index: -1;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background-image: 
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid rgba(102, 126, 234, 0.3);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #f093fb !important;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 80px rgba(240, 147, 251, 0.5);
        }

        .hero-subtitle {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.6);
            color: white;
        }

        .btn-secondary-custom {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-3px);
            color: white;
        }

        /* Lottery Balls Animation */
        .lottery-visual {
            position: relative;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lottery-machine {
            width: 100%;
            height: 100%;
            position: relative;
            background: url('https://images.unsplash.com/photo-1533900298318-6b8da08a523e?w=800&h=600&fit=crop') center/contain no-repeat;
            filter: brightness(1.2) contrast(1.1);
            opacity: 0.9;
        }

        .lottery-ball {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 900;
            color: white;
            position: absolute;
            animation: float 3s ease-in-out infinite;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5), inset 0 -5px 20px rgba(0,0,0,0.3);
            border: 3px solid rgba(255,255,255,0.3);
        }

        .lottery-ball::after {
            content: '';
            position: absolute;
            top: 15%;
            left: 20%;
            width: 30%;
            height: 30%;
            background: rgba(255,255,255,0.4);
            border-radius: 50%;
            filter: blur(8px);
        }

        .lottery-ball:nth-child(1) {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            top: 15%;
            left: 15%;
            animation-delay: 0s;
        }

        .lottery-ball:nth-child(2) {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            top: 55%;
            left: 10%;
            animation-delay: 0.5s;
        }

        .lottery-ball:nth-child(3) {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            top: 35%;
            right: 20%;
            animation-delay: 1s;
        }

        .lottery-ball:nth-child(4) {
            background: linear-gradient(135deg, #10b981, #059669);
            top: 65%;
            right: 15%;
            animation-delay: 1.5s;
        }

        .lottery-ball:nth-child(5) {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            top: 10%;
            right: 40%;
            animation-delay: 2s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(-30px) rotate(180deg) scale(1.1); }
        }

        /* Stats Bar */
        .stats-bar {
            background: rgba(15, 15, 35, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin: -50px auto 80px;
            position: relative;
            z-index: 10;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .stats-bar::before {
            content: '💰';
            position: absolute;
            font-size: 10rem;
            opacity: 0.05;
            top: 50%;
            right: 5%;
            transform: translateY(-50%) rotate(-15deg);
        }

        .stats-bar::after {
            content: '🏆';
            position: absolute;
            font-size: 10rem;
            opacity: 0.05;
            top: 50%;
            left: 5%;
            transform: translateY(-50%) rotate(15deg);
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
        }

        .stat-label {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0.5rem;
        }

        /* Section Titles */
        .section-title {
            font-size: 3rem;
            font-weight: 900;
            text-align: center;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Campaign Cards */
        .campaign-card {
            background: rgba(15, 15, 35, 0.6);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            position: relative;
        }

        .campaign-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .campaign-card:hover {
            transform: translateY(-10px);
            border-color: rgba(240, 147, 251, 0.6);
            box-shadow: 0 20px 60px rgba(240, 147, 251, 0.3);
        }

        .campaign-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .campaign-header::before {
            content: '🎟️';
            position: absolute;
            font-size: 8rem;
            opacity: 0.1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
        }

        .campaign-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .campaign-name {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .campaign-price {
            font-size: 3rem;
            font-weight: 900;
            margin: 1rem 0;
            text-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .campaign-body {
            padding: 2rem;
        }

        .campaign-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .info-box {
            background: rgba(102, 126, 234, 0.1);
            padding: 1rem;
            border-radius: 15px;
            text-align: center;
        }

        .info-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #f093fb;
        }

        .btn-campaign {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 15px;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .btn-campaign:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            color: white;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: rgba(15, 15, 35, 0.5);
            position: relative;
            overflow: hidden;
        }

        .features-section::before {
            content: '';
            position: absolute;
            top: 10%;
            left: -5%;
            width: 200px;
            height: 130px;
            background: url('https://images.unsplash.com/photo-1607863680198-23d4b2565df0?w=400&h=300&fit=crop') center/cover;
            border-radius: 15px;
            opacity: 0.1;
            transform: rotate(-20deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .features-section::after {
            content: '';
            position: absolute;
            bottom: 10%;
            right: -5%;
            width: 200px;
            height: 130px;
            background: url('https://images.unsplash.com/photo-1596838132731-3301c3fd4317?w=400&h=300&fit=crop') center/cover;
            border-radius: 15px;
            opacity: 0.1;
            transform: rotate(20deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .feature-card {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }

        .feature-card:hover {
            border-color: rgba(240, 147, 251, 0.6);
            transform: translateY(-5px);
            background: rgba(102, 126, 234, 0.2);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-desc {
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
        }

        /* How It Works */
        .how-it-works {
            padding: 80px 0;
            position: relative;
        }

        .how-it-works::before {
            content: '🎰';
            position: absolute;
            font-size: 15rem;
            opacity: 0.05;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .step-card {
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .step-number {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 900;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .step-desc {
            color: rgba(255,255,255,0.7);
        }

        /* Promo Banner */
        .promo-banner {
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
            padding: 60px 0;
            margin: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .promo-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1596838132731-3301c3fd4317?w=1200&h=400&fit=crop') center/cover;
            opacity: 0.15;
            filter: blur(2px);
        }

        .promo-banner::after {
            content: '🎰🎲🎯🏆💰🎟️🎊✨';
            position: absolute;
            font-size: 5rem;
            opacity: 0.1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            animation: scroll 20s linear infinite;
        }

        @keyframes scroll {
            0% { transform: translate(-50%, -50%) translateX(0); }
            100% { transform: translate(-50%, -50%) translateX(-100%); }
        }

        /* Scratch Ticket Decorations */
        .scratch-ticket {
            position: absolute;
            width: 150px;
            height: 100px;
            background: url('https://images.unsplash.com/photo-1607863680198-23d4b2565df0?w=300&h=200&fit=crop') center/cover;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transform: rotate(-15deg);
            opacity: 0.2;
            animation: float 4s ease-in-out infinite;
        }

        .scratch-ticket:nth-child(2) {
            right: 5%;
            top: 20%;
            transform: rotate(15deg);
            animation-delay: 1s;
        }

        .promo-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .promo-title {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .promo-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer-custom {
            background: rgba(15, 15, 35, 0.95);
            border-top: 2px solid rgba(102, 126, 234, 0.3);
            padding: 60px 0 30px;
        }

        .footer-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #f093fb;
        }

        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .footer-link:hover {
            color: #f093fb;
            padding-left: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .lottery-visual {
                height: 250px;
            }

            .lottery-ball {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .stats-bar {
                margin: 0 1rem 40px;
            }

            .stat-value {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= url('public') ?>">
                <i class="fas fa-ticket"></i>
                eTickets Raffle
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('public') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('public/howToPlay') ?>">How to Play</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('public/winners') ?>">Winners</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary-custom" href="<?= url('public/buyTicket') ?>" style="padding: 0.5rem 1.5rem; margin-left: 1rem;">
                            <i class="fas fa-ticket"></i> Buy Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-fire"></i> LIVE DRAWS DAILY
                    </div>
                    <h1 class="hero-title">
                        Win Big with eTickets Raffle
                    </h1>
                    <p class="hero-subtitle">
                        Join thousands of winners! Play exciting lottery games, win amazing cash prizes, and change your life today.
                    </p>
                    <div class="hero-cta">
                        <a href="<?= url('public/buyTicket') ?>" class="btn btn-primary-custom">
                            <i class="fas fa-ticket"></i> Buy Tickets Now
                        </a>
                        <a href="#campaigns" class="btn btn-secondary-custom">
                            <i class="fas fa-list"></i> View Games
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="lottery-visual">
                        <div class="lottery-machine"></div>
                        <div class="lottery-ball">7</div>
                        <div class="lottery-ball">3</div>
                        <div class="lottery-ball">9</div>
                        <div class="lottery-ball">5</div>
                        <div class="lottery-ball">1</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <div class="container">
        <div class="stats-bar">
            <div class="row">
                <div class="col-md-4 stat-item">
                    <span class="stat-value">
                        <i class="fas fa-trophy"></i> <?= number_format($stats['total_winners'] ?? 0) ?>+
                    </span>
                    <span class="stat-label">Winners</span>
                </div>
                <div class="col-md-4 stat-item">
                    <span class="stat-value">
                        <i class="fas fa-coins"></i> GHS <?= number_format($stats['total_prizes'] ?? 0, 0) ?>
                    </span>
                    <span class="stat-label">Prizes Awarded</span>
                </div>
                <div class="col-md-4 stat-item">
                    <span class="stat-value">
                        <i class="fas fa-fire"></i> <?= count($campaigns ?? []) ?>
                    </span>
                    <span class="stat-label">Active Games</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Campaigns -->
    <section class="container mb-5" id="campaigns" style="position: relative;">
        <div style="position: absolute; top: -50px; right: 10%; width: 100px; height: 100px; font-size: 5rem; opacity: 0.1; animation: float 3s ease-in-out infinite;">🎲</div>
        <div style="position: absolute; bottom: 20%; left: 5%; width: 100px; height: 100px; font-size: 5rem; opacity: 0.1; animation: float 3s ease-in-out infinite; animation-delay: 1s;">🎯</div>
        
        <h2 class="section-title">
            <i class="fas fa-gamepad"></i> Active Lottery Games
        </h2>
        
        <div class="row">
            <?php if (!empty($campaigns)): ?>
                <?php foreach ($campaigns as $campaign): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="campaign-card">
                            <?php if ($campaign->daily_draw_enabled): ?>
                                <div class="campaign-badge">
                                    <i class="fas fa-bolt"></i> DAILY DRAWS
                                </div>
                            <?php endif; ?>
                            
                            <div class="campaign-header">
                                <h3 class="campaign-name"><?= htmlspecialchars($campaign->name) ?></h3>
                                <div class="campaign-price">
                                    <?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?>
                                </div>
                                <small>per ticket</small>
                            </div>
                            
                            <div class="campaign-body">
                                <p style="color: rgba(255,255,255,0.8); margin-bottom: 1.5rem;">
                                    <?= htmlspecialchars(substr($campaign->description ?? 'Play and win amazing prizes!', 0, 100)) ?>...
                                </p>
                                
                                <div class="campaign-info-grid">
                                    <div class="info-box">
                                        <div class="info-label">Prize Pool</div>
                                        <div class="info-value"><?= $campaign->prize_pool_percent ?>%</div>
                                    </div>
                                    <div class="info-box">
                                        <div class="info-label">Ends</div>
                                        <div class="info-value"><?= formatDate($campaign->end_date, 'M d') ?></div>
                                    </div>
                                </div>
                                
                                <a href="<?= url('public/campaign/' . $campaign->id) ?>" class="btn btn-campaign">
                                    <i class="fas fa-play-circle"></i> Play Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No active games at the moment. Check back soon!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Promo Banner -->
    <div class="promo-banner">
        <div class="container promo-content">
            <h2 class="promo-title">🎉 Win Up To GHS 10,000!</h2>
            <p class="promo-subtitle">Daily draws with instant winners. Your luck starts here!</p>
            <a href="<?= url('public/buyTicket') ?>" class="btn btn-primary-custom btn-lg">
                <i class="fas fa-rocket"></i> Start Playing Now
            </a>
        </div>
    </div>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose eTickets?</h2>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <h3 class="feature-title">100% Secure</h3>
                        <p class="feature-desc">Bank-level security with encrypted transactions. Your money and data are safe with us.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="feature-title">Instant Tickets</h3>
                        <p class="feature-desc">Get your ticket codes immediately via SMS. No waiting, start playing right away!</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3 class="feature-title">Daily Draws</h3>
                        <p class="feature-desc">Multiple chances to win every day. More draws mean more opportunities for you!</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">Play via USSD</h3>
                        <p class="feature-desc">No internet? No problem! Dial our USSD code and play from any phone.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="feature-title">Instant Notifications</h3>
                        <p class="feature-desc">Winners are notified immediately via SMS. Never miss your winning moment!</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <h3 class="feature-title">Quick Payouts</h3>
                        <p class="feature-desc">Get your winnings fast! We process payouts quickly and securely.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3 class="step-title">Choose Your Game</h3>
                        <p class="step-desc">Browse our exciting lottery games and pick your favorite</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3 class="step-title">Buy Tickets</h3>
                        <p class="step-desc">Purchase tickets easily via mobile money or online payment</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3 class="step-title">Wait for Draw</h3>
                        <p class="step-desc">Daily and final draws are conducted transparently</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3 class="step-title">Win Big!</h3>
                        <p class="step-desc">Winners receive instant SMS notification and quick payouts</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">
                        <i class="fas fa-ticket"></i> eTickets Raffle
                    </h5>
                    <p style="color: rgba(255,255,255,0.7);">
                        Ghana's most trusted lottery platform. Play responsibly and win big with eTickets!
                    </p>
                    <div style="font-size: 1.5rem; margin-top: 1rem;">
                        <a href="#" style="color: rgba(255,255,255,0.7); margin-right: 1rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: rgba(255,255,255,0.7); margin-right: 1rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: rgba(255,255,255,0.7); margin-right: 1rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: rgba(255,255,255,0.7);"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <a href="<?= url('public') ?>" class="footer-link">Home</a>
                    <a href="<?= url('public/howToPlay') ?>" class="footer-link">How to Play</a>
                    <a href="<?= url('public/winners') ?>" class="footer-link">Winners</a>
                    <a href="<?= url('public/buyTicket') ?>" class="footer-link">Buy Tickets</a>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <p style="color: rgba(255,255,255,0.7);">
                        <i class="fas fa-phone"></i> +233 XX XXX XXXX<br>
                        <i class="fas fa-envelope"></i> support@etickets.com<br>
                        <i class="fas fa-map-marker-alt"></i> Accra, Ghana
                    </p>
                </div>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0;">
            
            <div class="text-center" style="color: rgba(255,255,255,0.5);">
                <p>&copy; <?= date('Y') ?> eTickets Raffle. All rights reserved. | Play Responsibly | 18+</p>
            </div>
        </div>
    </footer>

    <script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
    <script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    // Smooth scroll
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    // Add counting animation to stats
    $('.stat-value').each(function() {
        var $this = $(this);
        var text = $this.text();
        var match = text.match(/[\d,]+/);
        if (match) {
            var countTo = parseInt(match[0].replace(/,/g, ''));
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.html($this.html().replace(/[\d,]+/, Math.floor(this.countNum).toLocaleString()));
                },
                complete: function() {
                    $this.html($this.html().replace(/[\d,]+/, this.countNum.toLocaleString()));
                }
            });
        }
    });
    </script>
</body>
</html>
