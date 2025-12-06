<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Verify Ticket</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Verify Ticket</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Enter Ticket Code</h3>
                        </div>
                        <form method="POST" action="<?= url('ticket/verify') ?>">
                            <?= csrf_field() ?>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="ticket_code">Ticket Code</label>
                                    <input type="text" class="form-control form-control-lg text-center" 
                                           id="ticket_code" name="ticket_code" 
                                           placeholder="e.g., DEC24-HFM-00001" 
                                           style="letter-spacing: 2px; font-weight: bold;" required>
                                </div>
                            </div>
                            <div class="card-footer text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search"></i> Verify Ticket
                                </button>
                            </div>
                        </form>
                    </div>

                    <?php if (isset($result)): ?>
                        <?php if ($result['valid']): ?>
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-check-circle"></i> Valid Ticket</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Ticket Code:</th>
                                            <td><strong><?= htmlspecialchars($result['ticket']->ticket_code) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th>Campaign:</th>
                                            <td><?= htmlspecialchars($result['ticket']->campaign_name) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Player:</th>
                                            <td><?= htmlspecialchars($result['ticket']->player_phone) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <?php if ($result['is_winner']): ?>
                                                    <span class="badge badge-success">WINNER!</span>
                                                    <br>Prize: GHS <?= number_format($result['prize_amount'], 2) ?>
                                                <?php else: ?>
                                                    <span class="badge badge-info">Valid Entry</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle"></i> <?= $result['message'] ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
