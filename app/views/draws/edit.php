<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Draw</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('draw') ?>">Draws</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('draw/show/' . $draw->id) ?>">Draw #<?= $draw->id ?></a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Draw Information</h3>
                        </div>
                        <form action="<?= url('draw/edit/' . $draw->id) ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Campaign</h5>
                                    <strong><?= htmlspecialchars($campaign->name ?? 'N/A') ?></strong>
                                    <p class="mb-0">You can only edit the draw date, type, and winner count</p>
                                </div>

                                <div class="form-group">
                                    <label for="draw_date">Draw Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="draw_date" name="draw_date" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($draw->draw_date)) ?>" required>
                                    <small class="form-text text-muted">When should this draw be conducted?</small>
                                </div>

                                <div class="form-group">
                                    <label for="draw_type">Draw Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="draw_type" name="draw_type" required>
                                        <option value="daily" <?= $draw->draw_type === 'daily' ? 'selected' : '' ?>>Daily Draw</option>
                                        <option value="weekly" <?= $draw->draw_type === 'weekly' ? 'selected' : '' ?>>Weekly Draw</option>
                                        <option value="monthly" <?= $draw->draw_type === 'monthly' ? 'selected' : '' ?>>Monthly Draw</option>
                                        <option value="special" <?= $draw->draw_type === 'special' ? 'selected' : '' ?>>Special Draw</option>
                                        <option value="grand" <?= $draw->draw_type === 'grand' ? 'selected' : '' ?>>Grand Draw</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="winner_count">Number of Winners <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="winner_count" name="winner_count" 
                                           value="<?= $draw->winner_count ?? 1 ?>" min="1" max="100" required>
                                    <small class="form-text text-muted">How many winners should be selected?</small>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Draw
                                </button>
                                <a href="<?= url('draw/show/' . $draw->id) ?>" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Important Notes</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-info"></i> 
                                    Only pending draws can be edited
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-warning"></i> 
                                    Changing the date will reschedule the draw
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-trophy text-success"></i> 
                                    Winner count affects prize distribution
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-danger"></i> 
                                    Cannot edit after draw is conducted
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Current Status</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Status:</strong> 
                                <span class="badge badge-warning"><?= ucfirst($draw->status) ?></span>
                            </p>
                            <p><strong>Created:</strong> <?= formatDate($draw->created_at) ?></p>
                            <p><strong>Campaign:</strong> <?= htmlspecialchars($campaign->name ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
