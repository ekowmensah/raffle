<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> | Reset Password</title>
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
            <p class="login-box-msg">Enter your new password</p>

            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= url('auth/resetPassword') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="New Password" required minlength="6">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required minlength="6">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= vendor('adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= vendor('adminlte/dist/js/adminlte.min.js') ?>"></script>
</body>
</html>
