<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi database sudah dibuat
if (!isset($conn)) {
    include_once 'includes/db.php';
}

// Ambil statistik dari database
$stats = [
    'users' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM user"))['count'],
    'ponds' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM data_kolam"))['count'],
    'fish' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM data_ikan"))['count'],
    'feed' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM data_pakan"))['count'],
    'seedlings' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM tebar_bibit"))['count'],
    'maintenance' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pemeliharaan_ikan"))['count'],
    'feed_cost' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM biaya_pakan"))['count'],
    'support_cost' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM biaya_pendukung"))['count'],
    'harvests' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM panen"))['count']
];

// Tampilkan pesan selamat datang jika ada
if (isset($_SESSION['welcome_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['welcome_message'] . "</div>";
    unset($_SESSION['welcome_message']);
}
?>

<h2>Dashboard Admin</h2>
<div class="dashboard-container">
    <div class="dashboard-welcome">
        <h3>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
        <p>akjwdhawkjldhjwakjdhnajkdhajkwdhbajkdhajkdhakjwdhakjwhdawjkdhwakj</p>
    </div>
    
    <div class="dashboard-stats">
        <a href="dashboard-admin.php?page=manage-users" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['users']; ?></h4>
                    <p>Total Pengguna</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-kolam" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-water"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['ponds']; ?></h4>
                    <p>Data Kolam</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-ikan" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-fish"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['fish']; ?></h4>
                    <p>Data Ikan</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-pakan" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-drumstick-bite"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['feed']; ?></h4>
                    <p>Data Pakan</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-tebar-bibit" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-seedling"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['seedlings']; ?></h4>
                    <p>Data Tebar Bibit</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-pemeliharaan" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tasks"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['maintenance']; ?></h4>
                    <p>Data Pemeliharaan</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-biaya-pakan" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-coins"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['feed_cost']; ?></h4>
                    <p>Data Biaya Pakan</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-biaya-pendukung" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['support_cost']; ?></h4>
                    <p>Data Biaya Pendukung</p>
                </div>
            </div>
        </a>
        <a href="dashboard-admin.php?page=data-panen" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-content">
                    <h4><?php echo $stats['harvests']; ?></h4>
                    <p>Data Panen</p>
                </div>
            </div>
        </a>
    </div>
    
    <!-- <div class="dashboard-actions">
        <h3>Aksi Cepat</h3>
        <div class="action-buttons">
            <a href="dashboard-admin.php?page=input-data-ikan" class="action-button">
                <i class="fas fa-plus"></i> Tambah Data Ikan
            </a>
            <a href="dashboard-admin.php?page=input-data-kolam" class="action-button">
                <i class="fas fa-plus"></i> Tambah Data Kolam
            </a>
            <a href="dashboard-admin.php?page=input-data-pakan" class="action-button">
                <i class="fas fa-plus"></i> Tambah Data Pakan
            </a>
            <a href="dashboard-admin.php?page=input-data-tebar-bibit" class="action-button">
                <i class="fas fa-plus"></i> Tambah Data Tebar Bibit
            </a>
            <a href="dashboard-admin.php?page=input-data-pemeliharaan" class="action-button">
                <i class="fas fa-plus"></i> Tambah Data Pemeliharaan
            </a>
            <a href="dashboard-admin.php?page=input-data-biaya-pakan" class="action-button">
                <i class="fas fa-plus"></i> Tambah Data Biaya Pakan
            </a>
            <a href="dashboard-admin.php?page=input-data-biaya-pendukung" class="action-button">
                <i class="fas fa-plus"></i> Tambah Data Biaya Pendukung
            </a>
            <a href="dashboard-admin.php?page=input-data-panen" class="action-button">
                <i class="fas fa-plus"></i> Catat Panen
            </a>
        </div>
    </div> -->
    
    <div class="dashboard-recent">
        <h3>Data Terbaru</h3>
        <div class="recent-data">
            <div class="recent-panen">
                <h4>Panen Terakhir</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Ikan</th>
                            <th>Berat Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_panen = mysqli_query($conn, "SELECT p.tanggal_panen, di.jenis_bibit, p.berat_total 
                                                            FROM panen p 
                                                            JOIN data_ikan di ON p.id_ikan = di.id_ikan 
                                                            ORDER BY p.tanggal_panen DESC LIMIT 5");
                        while ($row = mysqli_fetch_assoc($recent_panen)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['tanggal_panen']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['jenis_bibit']) . "</td>";
                            echo "<td>" . number_format($row['berat_total'], 2) . " kg</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>