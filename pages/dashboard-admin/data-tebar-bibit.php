<?php

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi database sudah dibuat
if (!isset($conn)) {
    include_once 'includes/db.php';
}

// Query untuk mengambil semua data tebar bibit
$query = "SELECT tb.id_tebar, tb.id_ikan, tb.id_kolam, tb.tanggal_tebar, 
                 di.jenis_bibit, tb.jumlah_bibit, tb.ukuran, tb.harga_bibit_satuan, tb.user_id 
          FROM tebar_bibit tb
          JOIN data_ikan di ON tb.id_ikan = di.id_ikan
          ORDER BY tb.tanggal_tebar DESC";

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

<h2>Data Tebar Bibit</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data Tebar Bibit...">
        <button id="search-button">Cari</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Tebar</th>
                    <th>ID Ikan</th>
                    <th>ID Kolam</th>
                    <th>Tanggal Tebar</th>
                    <th>Jenis Bibit</th>
                    <th>Jumlah Bibit</th>
                    <th>Ukuran</th>
                    <th>Harga Bibit Satuan</th>
                    <th>User ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_tebar']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_tebar']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_bibit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_bibit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ukuran']) . " cm</td>";
                        echo "<td>Rp " . number_format($row['harga_bibit_satuan'], 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-admin.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-tebar-bibit">
                                <input type="hidden" name="id" value="' . htmlspecialchars($row['id_tebar']) . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <button type="button" class="delete-button" 
                                    data-id="' . htmlspecialchars($row['id_tebar']) . '" 
                                    data-table="tebar_bibit" 
                                    data-return-page="data-tebar-bibit">Delete</button>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Tidak ada data tebar bibit</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>