<?php

$user_id = $_SESSION['user_id'];

// Query untuk mengambil data biaya pendukung
$query = "SELECT bp.*, pi.jenis_bibit, dk.jenis_kolam, pi.id_ikan
          FROM biaya_pendukung bp
          LEFT JOIN pemeliharaan_ikan pi ON bp.id_pemeliharaan = pi.id_pemeliharaan
          LEFT JOIN data_kolam dk ON bp.id_kolam = dk.id_kolam
          WHERE bp.user_id = ?
          ORDER BY bp.tanggal_tebar DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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

<h2>Biaya Pendukung</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data Biaya Pendukung...">
        <button id="search-button">Cari</button>
        <button id="add-data-button" onclick="window.location.href='dashboard-user.php?page=input-data-Biaya-Pendukung'">Tambah Data Biaya Pendukung</button>
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
                        echo '<td>
                            <form method="GET" action="dashboard-user.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-biaya-pendukung">
                                <input type="hidden" name="id" value="' . $row['id_biaya_pendukung'] . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <form method="POST" action="includes/delete_data.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . $row['id_biaya_pendukung'] . '">
                                <input type="hidden" name="table" value="biaya_pendukung">
                                <input type="hidden" name="return_page" value="biaya-pendukung">
                                <button type="button" class="delete-button" data-id="' . $row['id_biaya_pendukung'] . '" data-table="biaya_pendukung" data-return-page="biaya-pendukung">Delete</button>
                            </form>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Tidak ada data biaya pendukung</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>