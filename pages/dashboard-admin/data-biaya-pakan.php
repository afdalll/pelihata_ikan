<?php

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi database sudah dibuat
if (!isset($conn)) {
    include_once 'includes/db.php';
}

// Query untuk mengambil semua data biaya pakan
$query = "SELECT bp.*, pi.jenis_bibit, dk.jenis_kolam, dp.nama_pakan AS nama_pakan_aktual, 
          dp.harga_per_kilo, pi.pakan_harian, pi.jumlah_hidup, pi.usia_pemeliharaan,
          (bp.total_digunakan * dp.harga_per_kilo) AS total_biaya_pakan_terbaru, bp.user_id
          FROM biaya_pakan bp
          LEFT JOIN pemeliharaan_ikan pi ON bp.id_pemeliharaan = pi.id_pemeliharaan
          LEFT JOIN data_kolam dk ON bp.id_kolam = dk.id_kolam
          LEFT JOIN data_pakan dp ON pi.nama_pakan = dp.nama_pakan AND pi.user_id = dp.user_id
          ORDER BY bp.tanggal_tebar DESC";

$result = mysqli_query($conn, $query);

// Debugging: Periksa hasil query
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Tampilkan pesan sukses atau error jika ada
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-" . $_SESSION['message_type'] . "'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<h2>Data Biaya Pakan</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data Biaya Pakan...">
        <button id="search-button">Cari</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Biaya Pakan</th>
                    <th>ID Ikan</th>
                    <th>ID Pemeliharaan</th>
                    <th>ID Kolam</th>
                    <th>Tanggal Tebar</th>
                    <th>Nama Pakan</th>
                    <th>Jenis Ikan</th>
                    <th>Total Digunakan</th>
                    <th>Total Biaya Pakan</th>
                    <th>User ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_biaya_pakan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_pemeliharaan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_tebar']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_pakan_aktual'] ?? $row['nama_pakan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_ikan']) . "</td>";
                        
                        // Gunakan total_digunakan dari database
                        $total_digunakan = $row['total_digunakan'];
                        echo "<td>" . number_format($total_digunakan, 2) . " kg</td>";
                        
                        // Gunakan total_biaya_pakan_terbaru yang dihitung dalam query
                        $total_biaya_pakan = $row['total_biaya_pakan_terbaru'];
                        echo "<td>Rp " . number_format($total_biaya_pakan, 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        
                        echo '<td>
                            <form method="POST" action="includes/delete_data.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . htmlspecialchars($row['id_biaya_pakan']) . '">
                                <input type="hidden" name="table" value="biaya_pakan">
                                <input type="hidden" name="return_page" value="data-biaya-pakan">
                                <button type="button" class="delete-button" 
                                        data-id="' . htmlspecialchars($row['id_biaya_pakan']) . '" 
                                        data-table="biaya_pakan" 
                                        data-return-page="data-biaya-pakan">Delete</button>
                            </form>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>Tidak ada data biaya pakan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>