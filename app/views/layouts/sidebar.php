    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= url('home') ?>" class="brand-link">
            <img src="<?= vendor('adminlte/img/AdminLTELogo.png') ?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light"><?= APP_NAME ?></span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?= vendor('adminlte/img/user2-160x160.jpg') ?>" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">
                        <?= $_SESSION['user']->name ?? 'User' ?>
                        <br>
                        <small class="badge badge-info"><?= getRoleDisplayName($_SESSION['user']->role_name ?? '') ?></small>
                    </a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <!-- Dashboard - All Roles -->
                    <li class="nav-item">
                        <a href="<?= url('home') ?>" class="nav-link <?= isActive('home') ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- ==================== SUPER ADMIN MENU ==================== -->
                    <?php if (hasRole('super_admin')): ?>
                    
                    <li class="nav-header">SYSTEM MANAGEMENT</li>
                    <li class="nav-item">
                        <a href="<?= url('user') ?>" class="nav-link <?= isActive('user') ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('role') ?>" class="nav-link <?= isActive('role') ?>">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Roles</p>
                        </a>
                    </li>

                    <li class="nav-header">ORGANIZATION</li>
                    <li class="nav-item">
                        <a href="<?= url('station') ?>" class="nav-link <?= isActive('station') ?>">
                            <i class="nav-icon fas fa-broadcast-tower"></i>
                            <p>Stations</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('programme') ?>" class="nav-link <?= isActive('programme') ?>">
                            <i class="nav-icon fas fa-microphone"></i>
                            <p>Programmes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('sponsor') ?>" class="nav-link <?= isActive('sponsor') ?>">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>Sponsors</p>
                        </a>
                    </li>

                    <li class="nav-header">CAMPAIGNS & DRAWS</li>
                    <li class="nav-item">
                        <a href="<?= url('campaign') ?>" class="nav-link <?= isActive('campaign') ?>">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Campaigns</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('draw') ?>" class="nav-link <?= isActive('draw') ?>">
                            <i class="nav-icon fas fa-random"></i>
                            <p>Draws</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('draw/winners') ?>" class="nav-link <?= isActive('draw/winners') ?>">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Winners</p>
                        </a>
                    </li>

                    <li class="nav-header">PAYMENTS & TICKETS</li>
                    <li class="nav-item">
                        <a href="<?= url('payment') ?>" class="nav-link <?= isActive('payment') ?>">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('ticket') ?>" class="nav-link <?= isActive('ticket') ?>">
                            <i class="nav-icon fas fa-ticket-alt"></i>
                            <p>Tickets</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('player') ?>" class="nav-link <?= isActive('player') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Players</p>
                        </a>
                    </li>

                    <li class="nav-header">ANALYTICS</li>
                    <li class="nav-item">
                        <a href="<?= url('analytics') ?>" class="nav-link <?= isActive('analytics') ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Analytics Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">SECURITY & AUDIT</li>
                    <li class="nav-item">
                        <a href="<?= url('security') ?>" class="nav-link <?= isActive('security') ?>">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Security Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('audit') ?>" class="nav-link <?= isActive('audit') ?>">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Audit Logs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('cache') ?>" class="nav-link <?= isActive('cache') ?>">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Cache Management</p>
                        </a>
                    </li>

                    <?php endif; ?>

                    <!-- ==================== STATION ADMIN MENU ==================== -->
                    <?php if (hasRole('station_admin')): ?>
                    
                    <li class="nav-header">STATION MANAGEMENT</li>
                    <li class="nav-item">
                        <a href="<?= url('programme') ?>" class="nav-link <?= isActive('programme') ?>">
                            <i class="nav-icon fas fa-microphone"></i>
                            <p>Programmes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('campaign') ?>" class="nav-link <?= isActive('campaign') ?>">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Campaigns</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('user') ?>" class="nav-link <?= isActive('user') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Station Users</p>
                        </a>
                    </li>

                    <li class="nav-header">DRAWS & WINNERS</li>
                    <li class="nav-item">
                        <a href="<?= url('draw') ?>" class="nav-link <?= isActive('draw') ?>">
                            <i class="nav-icon fas fa-random"></i>
                            <p>All Draws</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('draw/winners') ?>" class="nav-link <?= isActive('draw/winners') ?>">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Winners</p>
                        </a>
                    </li>

                    <li class="nav-header">PAYMENTS & PLAYERS</li>
                    <li class="nav-item">
                        <a href="<?= url('payment') ?>" class="nav-link <?= isActive('payment') ?>">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('ticket') ?>" class="nav-link <?= isActive('ticket') ?>">
                            <i class="nav-icon fas fa-ticket-alt"></i>
                            <p>Tickets</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('withdrawal') ?>" class="nav-link <?= isActive('withdrawal') ?>">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Withdrawals</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('player') ?>" class="nav-link <?= isActive('player') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Players</p>
                        </a>
                    </li>

                    <li class="nav-header">REPORTS</li>
                    <li class="nav-item">
                        <a href="<?= url('analytics') ?>" class="nav-link <?= isActive('analytics') ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Analytics</p>
                        </a>
                    </li>

                    <?php endif; ?>

                    <!-- ==================== PROGRAMME MANAGER MENU ==================== -->
                    <?php if (hasRole('programme_manager')): ?>
                    
                    <li class="nav-header">DRAW MANAGEMENT</li>
                    <li class="nav-item">
                        <a href="<?= url('draw/pending') ?>" class="nav-link <?= isActive('draw/pending') ?>">
                            <i class="nav-icon fas fa-clock text-danger"></i>
                            <p>
                                Pending Draws
                                <?php 
                                $pendingCount = getPendingDrawsCount();
                                if ($pendingCount > 0): 
                                ?>
                                    <span class="badge badge-danger right"><?= $pendingCount ?></span>
                                <?php endif; ?>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('draw') ?>" class="nav-link <?= isActive('draw') ?>">
                            <i class="nav-icon fas fa-random"></i>
                            <p>All Draws</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('draw/schedule') ?>" class="nav-link <?= isActive('draw/schedule') ?>">
                            <i class="nav-icon fas fa-calendar-plus"></i>
                            <p>Schedule Draw</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('draw/winners') ?>" class="nav-link <?= isActive('draw/winners') ?>">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Winners</p>
                        </a>
                    </li>

                    <li class="nav-header">CAMPAIGNS</li>
                    <li class="nav-item">
                        <a href="<?= url('campaign') ?>" class="nav-link <?= isActive('campaign') ?>">
                            <i class="nav-icon fas fa-bullhorn"></i>
                            <p>My Campaigns</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('campaign/create') ?>" class="nav-link <?= isActive('campaign/create') ?>">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Create Campaign</p>
                        </a>
                    </li>

                    <li class="nav-header">TICKETS & PLAYERS</li>
                    <li class="nav-item">
                        <a href="<?= url('ticket') ?>" class="nav-link <?= isActive('ticket') ?>">
                            <i class="nav-icon fas fa-ticket-alt"></i>
                            <p>Tickets</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('ticket/verify') ?>" class="nav-link <?= isActive('ticket/verify') ?>">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>Verify Ticket</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('player') ?>" class="nav-link <?= isActive('player') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Players</p>
                        </a>
                    </li>

                    <li class="nav-header">ANALYTICS</li>
                    <li class="nav-item">
                        <a href="<?= url('analytics') ?>" class="nav-link <?= isActive('analytics') ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Programme Analytics</p>
                        </a>
                    </li>

                    <?php endif; ?>

                    <!-- ==================== FINANCE MENU ==================== -->
                    <?php if (hasRole('finance')): ?>
                    
                    <li class="nav-header">PAYMENTS</li>
                    <li class="nav-item">
                        <a href="<?= url('payment') ?>" class="nav-link <?= isActive('payment') ?>">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>All Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('payment?status=pending') ?>" class="nav-link">
                            <i class="nav-icon fas fa-clock text-warning"></i>
                            <p>Pending Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('payment?status=success') ?>" class="nav-link">
                            <i class="nav-icon fas fa-check-circle text-success"></i>
                            <p>Successful Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('payment/manual') ?>" class="nav-link <?= isActive('payment/manual') ?>">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Manual Payment</p>
                        </a>
                    </li>

                    <li class="nav-header">FINANCIAL MANAGEMENT</li>
                    <li class="nav-item">
                        <a href="<?= url('wallet') ?>" class="nav-link <?= isActive('wallet') ?>">
                            <i class="nav-icon fas fa-wallet"></i>
                            <p>Wallets</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('withdrawal') ?>" class="nav-link <?= isActive('withdrawal') ?>">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Withdrawals</p>
                            <?php
                            $withdrawalModel = new \App\Models\Withdrawal();
                            $pendingCount = $withdrawalModel->countPending();
                            if ($pendingCount > 0):
                            ?>
                                <span class="badge badge-warning right"><?= $pendingCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('reconciliation') ?>" class="nav-link <?= isActive('reconciliation') ?>">
                            <i class="nav-icon fas fa-balance-scale"></i>
                            <p>Reconciliation</p>
                        </a>
                    </li>

                    <li class="nav-header">REPORTS</li>
                    <li class="nav-item">
                        <a href="<?= url('report/revenue') ?>" class="nav-link <?= isActive('report/revenue') ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Revenue Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('financial/commissions') ?>" class="nav-link <?= isActive('financial/commissions') ?>">
                            <i class="nav-icon fas fa-percentage"></i>
                            <p>Commission Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('financial/payouts') ?>" class="nav-link <?= isActive('financial/payouts') ?>">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Payout Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('payment/export') ?>" class="nav-link">
                            <i class="nav-icon fas fa-download"></i>
                            <p>Export Data</p>
                        </a>
                    </li>

                    <?php endif; ?>

                    <!-- ==================== AUDITOR MENU ==================== -->
                    <?php if (hasRole('auditor')): ?>
                    
                    <li class="nav-header">AUDIT & COMPLIANCE</li>
                    <li class="nav-item">
                        <a href="<?= url('audit') ?>" class="nav-link <?= isActive('audit') ?>">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Audit Logs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('audit/stats') ?>" class="nav-link <?= isActive('audit/stats') ?>">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>Audit Statistics</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('security') ?>" class="nav-link <?= isActive('security') ?>">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Security Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">REPORTS</li>
                    <li class="nav-item">
                        <a href="<?= url('analytics') ?>" class="nav-link <?= isActive('analytics') ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Analytics</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('audit/export') ?>" class="nav-link">
                            <i class="nav-icon fas fa-download"></i>
                            <p>Export Logs</p>
                        </a>
                    </li>

                    <li class="nav-header">SYSTEM</li>
                    <li class="nav-item">
                        <a href="<?= url('cache') ?>" class="nav-link <?= isActive('cache') ?>">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Cache Management</p>
                        </a>
                    </li>

                    <?php endif; ?>

                    <!-- ==================== COMMON MENU ITEMS ==================== -->
                    <li class="nav-header">ACCOUNT</li>
                    <li class="nav-item">
                        <a href="<?= url('profile') ?>" class="nav-link <?= isActive('profile') ?>">
                            <i class="nav-icon fas fa-user"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('auth/logout') ?>" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
