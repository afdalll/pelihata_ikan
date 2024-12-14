<?php

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi database sudah dibuat
if (!isset($conn)) {
    include_once 'includes/db.php';
}

// Query untuk mengambil semua data biaya pendukung
$query = "SELECT bp.*, pi.jenis_bibit, dk.jenis_kolam, pi.id_ikan, bp.user_id
          FROM biaya_pendukung bp
          LEFT JOIN pemeliharaan_ikan pi ON bp.id_pemeliharaan = pi.id_pemeliharaan
          LEFT JOIN data_kolam dk ON bp.id_kolam = dk.id_kolam
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

<h2>Data Biaya Pendukung</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data Biaya Pendukung...">
        <button id="search-button">Cari</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Biaya Pendukung</th>
                    <th>ID Ikan</th>
                    <th>ID Pemeliharaan</th>
                    <th>Tanggal Tebar</th>
                    <th>ID Kolam</th>
                    <th>Obat</th>
                    <th>Suplemen</th>
                    <th>Lainnya</th>
                    <th>Total Biaya Pendukung</th>
                    <th>User ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_biaya_pendukung']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_pemeliharaan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_tebar']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                        echo "<td>Rp " . number_format($row['obat'], 2, ',', '.') . "</td>";
                        echo "<td>Rp " . number_format($row['suplemen'], 2, ',', '.') . "</td>";
                        echo "<td>Rp " . number_format($row['lainnya'], 2, ',', '.') . "</td>";
                        echo "<td>Rp " . number_format($row['total_biaya_pendukung'], 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-admin.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-biaya-pendukung">
                                <input type="hidden" name="id" value="' . htmlspecialchars($row['id_biaya_pendukung']) . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <form method="POST" action="includes/delete_data.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . htmlspecialchars($row['id_biaya_pendukung']) . '">
                                <input type="hidden" name="table" value="biaya_pendukung">
                                <input type="hidden" name="return_page" value="data-biaya-pendukung">
                                <button type="button" class="delete-button" 
                                        data-id="' . htmlspecialchars($row['id_biaya_pendukung']) . '" 
                                        data-table="biaya_pendukung" 
                                        data-return-page="data-biaya-pendukung">Delete</button>
                            </form>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>Tidak ada data biaya pendukung</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>