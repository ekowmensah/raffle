<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Play & Win' ?> | Raffle System</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap">
    <link rel="stylesheet" href="<?= vendor('fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor('adminlte/css/adminlte.min.css') ?>">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .hero-section {
            padding: 60px 0;
            color: white;
            text-align: center;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .campaign-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            height: 100%;
        }
        .campaign-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .campaign-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .campaign-price {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        .stats-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
        }
        .btn-play {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-play:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
            color: white;
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .footer-custom {
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= url('public') ?>">
            <i class="fas fa-trophy text-warning"></i>
            <strong>Raffle System</strong>
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
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero-section" style="margin-top: 80px;">
    <div class="container">
        <h1 class="hero-title">
            <i class="fas fa-gift"></i> Win Amazing Prizes!
        </h1>
        <p class="hero-subtitle">
            Play our exciting raffle campaigns and stand a chance to win big
        </p>
        <a href="<?= url('public/buyTicket') ?>" class="btn btn-play btn-lg">
            <i class="fas fa-ticket-alt"></i> Buy Tickets Now
        </a>
        <a href="#campaigns" class="btn btn-outline-light btn-lg ml-3">
            <i class="fas fa-list"></i> View Campaigns
        </a>
    </div>
</div>

<!-- Features Section -->
<div class="container mb-5">
    <div class="row">
        <div class="col-md-4 text-center mb-4">
            <div class="card p-4">
                <i class="fas fa-mobile-alt feature-icon"></i>
                <h4>Play Anywhere</h4>
                <p>Use your mobile phone to buy tickets via USSD or online</p>
            </div>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="card p-4">
                <i class="fas fa-trophy feature-icon"></i>
                <h4>Daily Draws</h4>
                <p>Multiple chances to win with daily and final draws</p>
            </div>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="card p-4">
                <i class="fas fa-shield-alt feature-icon"></i>
                <h4>100% Secure</h4>
                <p>Safe and transparent raffle system you can trust</p>
            </div>
        </div>
    </div>
</div>

<!-- Active Campaigns -->
<div class="container mb-5" id="campaigns">
    <h2 class="text-center text-white mb-5">
        <i class="fas fa-fire"></i> Active Campaigns
    </h2>
    
    <div class="row">
        <?php if (!empty($campaigns)): ?>
            <?php foreach ($campaigns as $campaign): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card campaign-card">
                        <div class="campaign-header">
                            <h3><?= htmlspecialchars($campaign->name) ?></h3>
                            <div class="campaign-price">
                                <?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?>
                            </div>
                            <small>per ticket</small>
                        </div>
                        <div class="card-body">
                            <p><?= htmlspecialchars(substr($campaign->description ?? 'Win amazing prizes!', 0, 100)) ?></p>
                            
                            <div class="stats-box">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <strong>Ends</strong><br>
                                        <small><?= formatDate($campaign->end_date, 'M d, Y') ?></small>
                                    </div>
                                    <div class="col-6">
                                        <strong>Prize Pool</strong><br>
                                        <small><?= $campaign->prize_pool_percent ?>%</small>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($campaign->sponsor_name)): ?>
                                <p class="text-center mt-3">
                                    <small class="text-muted">Sponsored by</small><br>
                                    <strong><?= htmlspecialchars($campaign->sponsor_name) ?></strong>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-center">
                            <a href="<?= url('public/campaign/' . $campaign->id) ?>" class="btn btn-play btn-block">
                                <i class="fas fa-ticket-alt"></i> Buy Tickets
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No active campaigns at the moment. Check back soon!
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- How It Works -->
<div class="container mb-5">
    <div class="card">
        <div class="card-body p-5">
            <h2 class="text-center mb-4">
                <i class="fas fa-question-circle text-primary"></i> How It Works
            </h2>
            <div class="row">
                <div class="col-md-3 text-center mb-3">
                    <div class="display-4 text-primary">1</div>
                    <h5>Choose Campaign</h5>
                    <p>Select your favorite raffle campaign</p>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="display-4 text-success">2</div>
                    <h5>Buy Tickets</h5>
                    <p>Purchase tickets via mobile money or online</p>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="display-4 text-warning">3</div>
                    <h5>Wait for Draw</h5>
                    <p>Daily and final draws are conducted</p>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="display-4 text-danger">4</div>
                    <h5>Win Prizes!</h5>
                    <p>Winners are notified via SMS</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer-custom">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5><i class="fas fa-trophy"></i> Raffle System</h5>
                <p>Your trusted platform for exciting raffle campaigns and amazing prizes.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= url('public') ?>" class="text-white">Home</a></li>
                    <li><a href="<?= url('public/howToPlay') ?>" class="text-white">How to Play</a></li>
                    <li><a href="<?= url('public/winners') ?>" class="text-white">Winners</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Contact Us</h5>
                <p>
                    <i class="fas fa-phone"></i> +233 XX XXX XXXX<br>
                    <i class="fas fa-envelope"></i> info@raffle.com
                </p>
            </div>
        </div>
        <hr class="bg-white">
        <div class="text-center">
            <p>&copy; <?= date('Y') ?> Raffle System. All rights reserved.</p>
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
</script>
</body>
</html>
