<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> | Forgot Password</title>
    <link rel="stylesheet" href="<?= vendor('adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor('adminlte/dist/css/adminlte.min.css') ?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b><?= APP_NAME ?></b>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Enter your email to reset your password</p>

            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= url('auth/forgotPassword') ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    </div>
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="<?= url('auth/login') ?>">Back to Login</a>
            </p>
        </div>
    </div>
</div>

<script src="<?= vendor('adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= vendor('adminlte/dist/js/adminlte.min.js') ?>"></script>
</body>
</html>
