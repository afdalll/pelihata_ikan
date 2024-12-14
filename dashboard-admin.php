<?php
// Tentukan halaman default
$page = 'home-dashboard';

// Periksa parameter halaman dari URL
if (isset($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
}

// Definisikan array untuk judul halaman
$page_titles = [
    'home-dashboard' => 'Dashboard Admin',
    'manage-users' => 'Manage Users',
    'manage-data' => 'Manage Data',
    'reports' => 'Reports',
    // Tambahkan halaman lain yang diperlukan untuk admin
];

// Set judul halaman
$title = isset($page_titles[$page]) ? $page_titles[$page] : 'Dashboard Admin';

include 'includes/dashboard-admin/header-dashboard.php';
?>

<div class="content">
    <?php
    // Batasi akses hanya ke folder dashboard-admin
    $pageFile = "pages/dashboard-admin/" . basename($page) . ".php";
    if (file_exists($pageFile)) {
        include $pageFile;
    } else {
        echo "<p>Halaman tidak ditemukan.</p>";
    }
    ?>
</div>

<?php include 'includes/dashboard-admin/footer-dashboard.php'; ?>