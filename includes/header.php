<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SYSTEM_NAME; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-paw"></i> <?php echo SYSTEM_NAME; ?></h3>
                <p>Gestao Completa</p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo BASE_URL; ?>pages/dashboard.php"
                        class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>pages/clientes.php"
                        class="<?php echo (basename($_SERVER['PHP_SELF']) == 'clientes.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Clientes</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>pages/pets.php"
                        class="<?php echo (basename($_SERVER['PHP_SELF']) == 'pets.php') ? 'active' : ''; ?>">
                        <i class="fas fa-dog"></i>
                        <span>Pets</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>pages/servicos.php"
                        class="<?php echo (basename($_SERVER['PHP_SELF']) == 'servicos.php') ? 'active' : ''; ?>">
                        <i class="fas fa-briefcase"></i>
                        <span>Servicos</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>pages/atendimentos.php"
                        class="<?php echo (basename($_SERVER['PHP_SELF']) == 'atendimentos.php') ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i>
                        <span>Atendimentos</span>
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>pages/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </a>
                </li>
            </ul>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <main class="main-content">

            <nav class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" type="button" aria-label="Abrir menu"
                        aria-controls="sidebar" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h4>
                </div>

                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo e(strtoupper(substr($_SESSION['user_name'], 0, 1))); ?>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo e($_SESSION['user_name']); ?></span>
                            <span class="user-role"><?php echo e(ucfirst($_SESSION['user_role'])); ?></span>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="content-container">
