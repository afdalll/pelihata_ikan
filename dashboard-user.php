<?php
// Tentukan halaman default
$page = 'home-dashboard';

// Periksa parameter halaman dari URL
if (isset($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
}

// Definisikan array untuk judul halaman
$page_titles = [
    'akun' => 'Pengaturan Akun',
    'home-dashboard' => 'Dashboard',
    'data-kolam' => 'Data Kolam',
    'data-ikan' => 'Data Ikan',
    'data-pakan' => 'Data Pakan',
    'tebar-bibit' => 'Tebar Bibit',
    'pemeliharaan-ikan' => 'Pemeliharaan Ikan',
    'biaya-pakan' => 'Biaya Pakan',
    'biaya-pendukung' => 'Biaya Pendukung',
    'panen' => 'Panen',
    'laporan-pemeliharaan' => 'Laporan Pemeliharaan',
    'laporan-panen' => 'Laporan Panen',

    
    // Halaman edit
    'edit-data-biaya-pendukung' => 'Edit Data Biaya Pendukung',
    'edit-data-ikan' => 'Edit Data Ikan',
    'edit-data-kolam' => 'Edit Data Kolam',
    'edit-data-pakan' => 'Edit Data Pakan',
    'edit-data-tebar-bibit' => 'Edit Data Tebar Bibit',
    'edit-data-pemeliharaan-ikan' => 'Edit Data Pemeliharaan Ikan',
    'edit-data-biaya-pendukung' => 'Edit Biaya Pendukung',
    'edit-data-panen' => 'Edit Data Panen',
    
    
    
    // Halaman input
    'input-data-biaya-pakan' => 'Input Data Biaya Pakan',
    'input-data-biaya-pendukung' => 'Input Data Biaya Pendukung',
    'input-data-ikan' => 'Input Data Ikan',
    'input-data-kolam' => 'Input Data Kolam',
    'input-data-pakan' => 'Input Data Pakan',
    'input-data-tebar-bibit' => 'Input Data Tebar Bibit',
    'input-data-Pemeliharaan-ikan' => 'Input Data Pemeliharaan Ikan',
    'input-data-biaya-pendukung' => 'Input Data Biaya Pendukung',
    'input-data-biaya-pakan' => 'Input Data Biaya pakan',
    'input-data-panen' => 'Input Data Panen',
];

// Set judul halaman
$title = isset($page_titles[$page]) ? $page_titles[$page] : 'Dashboard';

include 'includes/dashboard-user/header-dashboard.php';
?>

<div class="content">
    <?php
    // Batasi akses hanya ke folder dashboard-user
    $pageFile = "pages/dashboard-user/" . basename($page) . ".php";
    if (file_exists($pageFile)) {
        include $pageFile;
    } else {
        echo "<p>Halaman tidak ditemukan.</p>";
    }
    ?>
</div>

<?php include 'includes/dashboard-user/footer-dashboard.php'; ?>