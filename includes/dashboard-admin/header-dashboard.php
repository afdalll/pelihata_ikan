<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';
include 'includes/delete_data.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit();
}

// Tangani logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php?page=login');
    exit();
}

// Ambil username dari sesi jika ada
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<header class="top-nav">
    <button class="menu-toggle">&#9776;</button>
    <form id="logout-form" action="dashboard-admin.php" method="post" class="logout-form" onsubmit="return confirmLogout()">
        <input type="hidden" name="logout" value="1">
        <button type="submit" class="logout-button">Logout</button>
    </form>
</header>
<aside class="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-title"><?php echo $title; ?></span>
        <button class="close-menu"><i class="fas fa-times"></i></button>
    </div>
    <div class="sidebar-username">Logged in as: <?php echo htmlspecialchars($username); ?></div>
    <nav>
        <ul>
            <li><a href="dashboard-admin.php?page=home-dashboard"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="dashboard-admin.php?page=manage-users"><i class="fas fa-users"></i> Manage Users</a></li>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle"><i class="fas fa-database"></i> Manage Data <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="dashboard-admin.php?page=data-kolam"><i class="fas fa-water"></i> Data Kolam</a></li>
                    <li><a href="dashboard-admin.php?page=data-ikan"><i class="fas fa-fish"></i> Data Ikan</a></li>
                    <li><a href="dashboard-admin.php?page=data-pakan"><i class="fas fa-drumstick-bite"></i> Data Pakan</a></li>
                    <li><a href="dashboard-admin.php?page=data-tebar-bibit"><i class="fas fa-seedling"></i> Data Tebar Bibit</a></li>
                    <li><a href="dashboard-admin.php?page=data-pemeliharaan"><i class="fas fa-leaf"></i> Data Pemeliharaan</a></li>
                    <li><a href="dashboard-admin.php?page=data-biaya-pakan"><i class="fas fa-money-bill-wave"></i> Data Biaya Pakan</a></li>
                    <li><a href="dashboard-admin.php?page=data-biaya-pendukung"><i class="fas fa-money-bill-wave"></i> Data Biaya Pendukung</a></li>
                    <li><a href="dashboard-admin.php?page=data-panen"><i class="fas fa-clipboard-list"></i> Data Panen</a></li>
                </ul>
            </li>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle"><i class="fas fa-file-alt"></i> Reports <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="dashboard-admin.php?page=laporan-pemeliharaan"><i class="fas fa-clipboard-list"></i> Laporan Pemeliharaan</a></li>
                    <li><a href="dashboard-admin.php?page=laporan-panen"><i class="fas fa-fish"></i> Laporan Panen</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
<main class="main-content">
<script>
function confirmLogout() {
    return confirm("Apakah Anda ingin keluar?");
}
</script>