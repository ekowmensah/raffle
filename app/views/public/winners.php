<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recent Winners | Raffle System</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap">
    <link rel="stylesheet" href="<?= vendor('fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor('adminlte/css/adminlte.min.css') ?>">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .content-section {
            background: white;
            border-radius: 20px;
            padding: 50px;
            margin-top: 100px;
            margin-bottom: 50px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .winner-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .winner-card:hover {
            transform: translateY(-5px);
        }
        .trophy-icon {
            font-size: 3rem;
            color: #ffd700;
        }
    </style>
</head>
<body>

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
                <li class="nav-item active">
                    <a class="nav-link" href="<?= url('public/winners') ?>">Winners</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="content-section">
        <h1 class="text-center mb-5">
            <i class="fas fa-trophy text-warning"></i> Recent Winners
        </h1>

        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> Winners will be displayed here once draws are conducted
        </div>

        <!-- Example Winner Cards (will be populated from database) -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="winner-card">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <i class="fas fa-trophy trophy-icon"></i>
                        </div>
                        <div class="col-9">
                            <h4>Daily Draw Winner</h4>
                            <p class="mb-1"><strong>Player:</strong> +233 XX XXX 1234</p>
                            <p class="mb-1"><strong>Prize:</strong> GHS 500.00</p>
                            <p class="mb-0"><small>Campaign: December Raffle 2024</small></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="winner-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="row align-items-center">
                        <div class="col-3 text-center">
                            <i class="fas fa-trophy trophy-icon"></i>
                        </div>
                        <div class="col-9">
                            <h4>Grand Prize Winner</h4>
                            <p class="mb-1"><strong>Player:</strong> +233 XX XXX 5678</p>
                            <p class="mb-1"><strong>Prize:</strong> GHS 5,000.00</p>
                            <p class="mb-0"><small>Campaign: November Raffle 2024</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <h3>Want to be our next winner?</h3>
            <p class="lead">Join our active campaigns and stand a chance to win amazing prizes!</p>
            <a href="<?= url('public') ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-play-circle"></i> Play Now
            </a>
        </div>
    </div>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
