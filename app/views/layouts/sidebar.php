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
                    <a href="#" class="d-block"><?= $_SESSION['user']->name ?? 'User' ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="<?= url('home') ?>" class="nav-link <?= isActive('home') ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- User Management -->
                    <?php if (can('*') || hasRole('super_admin')): ?>
                    <li class="nav-header">USER MANAGEMENT</li>
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
                    <?php endif; ?>

                    <!-- Organization -->
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

                    <!-- Campaigns -->
                    <li class="nav-header">CAMPAIGNS</li>
                    <li class="nav-item">
                        <a href="<?= url('sponsor') ?>" class="nav-link <?= isActive('sponsor') ?>">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>Sponsors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('campaign') ?>" class="nav-link <?= isActive('campaign') ?>">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Campaigns</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('campaign/dashboard') ?>" class="nav-link <?= isActive('campaign/dashboard') ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Campaign Dashboard</p>
                        </a>
                    </li>

                    <!-- Payments -->
                    <li class="nav-header">PAYMENTS</li>
                    <li class="nav-item">
                        <a href="<?= url('payment') ?>" class="nav-link <?= isActive('payment') ?>">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>All Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('payment/manual') ?>" class="nav-link <?= isActive('payment/manual') ?>">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Manual Payment (Test)</p>
                        </a>
                    </li>
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
                        <a href="<?= url('promocode') ?>" class="nav-link <?= isActive('promocode') ?>">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Promo Codes</p>
                        </a>
                    </li>

                    <!-- Draws -->
                    <li class="nav-header">DRAWS & WINNERS</li>
                    <li class="nav-item">
                        <a href="<?= url('draw/pending') ?>" class="nav-link <?= isActive('draw/pending') ?>">
                            <i class="nav-icon fas fa-clock"></i>
                            <p>Pending Draws</p>
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

                    <!-- Players -->
                    <li class="nav-header">PLAYERS</li>
                    <li class="nav-item">
                        <a href="<?= url('player') ?>" class="nav-link <?= isActive('player') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>All Players</p>
                        </a>
                    </li>

                    <!-- Financial Management -->
                    <li class="nav-header">FINANCIAL MANAGEMENT</li>
                    <li class="nav-item">
                        <a href="<?= url('wallet') ?>" class="nav-link <?= isActive('wallet') ?>">
                            <i class="nav-icon fas fa-wallet"></i>
                            <p>Station Wallets</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>
                                Financial Reports
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= url('report/revenue') ?>" class="nav-link <?= isActive('report/revenue') ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Revenue Report</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url('financial/commissions') ?>" class="nav-link <?= isActive('financial/commissions') ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Commission Report</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url('financial/payouts') ?>" class="nav-link <?= isActive('financial/payouts') ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Payout Report</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= url('financial/profitability') ?>" class="nav-link <?= isActive('financial/profitability') ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Profitability Analysis</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('reconciliation') ?>" class="nav-link <?= isActive('reconciliation') ?>">
                            <i class="nav-icon fas fa-balance-scale"></i>
                            <p>Reconciliation</p>
                        </a>
                    </li>

                    <!-- Reports & Analytics -->
                    <li class="nav-header">ANALYTICS</li>
                    <li class="nav-item">
                        <a href="<?= url('analytics') ?>" class="nav-link <?= isActive('analytics') ?>">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Analytics Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('draw/analytics') ?>" class="nav-link <?= isActive('draw/analytics') ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Draw Analytics</p>
                        </a>
                    </li>

                    <!-- Security & Audit -->
                    <?php if (can('*') || hasRole('super_admin')): ?>
                    <li class="nav-header">SECURITY</li>
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
                        <a href="<?= url('audit/stats') ?>" class="nav-link <?= isActive('audit/stats') ?>">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>Audit Statistics</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('cache') ?>" class="nav-link <?= isActive('cache') ?>">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Cache Management</p>
                        </a>
                    </li>
                    <?php endif; ?>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
