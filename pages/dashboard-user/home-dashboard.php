<?php
$user_id = $_SESSION['user_id'];

// Fungsi untuk mengambil jumlah data dari tabel tertentu
function getDataCount($conn, $table, $user_id) {
    $query = "SELECT COUNT(*) as count FROM $table WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Mengambil jumlah data untuk setiap tabel
$kolam_count = getDataCount($conn, 'data_kolam', $user_id);
$ikan_count = getDataCount($conn, 'data_ikan', $user_id);
$pakan_count = getDataCount($conn, 'data_pakan', $user_id);
$tebar_bibit_count = getDataCount($conn, 'tebar_bibit', $user_id);
$pemeliharaan_count = getDataCount($conn, 'pemeliharaan_ikan', $user_id);
$biaya_pakan_count = getDataCount($conn, 'biaya_pakan', $user_id);
$biaya_pendukung_count = getDataCount($conn, 'biaya_pendukung', $user_id);
$panen_count = getDataCount($conn, 'panen', $user_id);

// Mengambil data panen terbaru
$query_panen = "SELECT p.id_panen, p.tanggal_panen, p.harga_total, di.jenis_bibit 
                FROM panen p 
                JOIN data_ikan di ON p.id_ikan = di.id_ikan 
                WHERE p.user_id = ? 
                ORDER BY p.tanggal_panen DESC 
                LIMIT 5";
$stmt_panen = mysqli_prepare($conn, $query_panen);
mysqli_stmt_bind_param($stmt_panen, "i", $user_id);
mysqli_stmt_execute($stmt_panen);
$result_panen = mysqli_stmt_get_result($stmt_panen);

// Mengambil total biaya pakan dan pendukung
$query_biaya = "SELECT 
                    (SELECT SUM(total_biaya_pakan) FROM biaya_pakan WHERE user_id = ?) as total_biaya_pakan,
                    (SELECT SUM(total_biaya_pendukung) FROM biaya_pendukung WHERE user_id = ?) as total_biaya_pendukung";
$stmt_biaya = mysqli_prepare($conn, $query_biaya);
mysqli_stmt_bind_param($stmt_biaya, "ii", $user_id, $user_id);
mysqli_stmt_execute($stmt_biaya);
$result_biaya = mysqli_stmt_get_result($stmt_biaya);
$row_biaya = mysqli_fetch_assoc($result_biaya);
$total_biaya_pakan = $row_biaya['total_biaya_pakan'];
$total_biaya_pendukung = $row_biaya['total_biaya_pendukung'];
?>

<h2>Dashboard</h2>

<div class="dashboard-summary">
    <div class="summary-box">
        <h3>Jumlah Data</h3>
        <ul>
            <li>Kolam: <?php echo $kolam_count; ?></li>
            <li>Ikan: <?php echo $ikan_count; ?></li>
            <li>Pakan: <?php echo $pakan_count; ?></li>
            <li>Tebar Bibit: <?php echo $tebar_bibit_count; ?></li>
            <li>Pemeliharaan: <?php echo $pemeliharaan_count; ?></li>
            <li>Biaya Pakan: <?php echo $biaya_pakan_count; ?></li>
            <li>Biaya Pendukung: <?php echo $biaya_pendukung_count; ?></li>
            <li>Panen: <?php echo $panen_count; ?></li>
        </ul>
    </div>

    <div class="summary-box">
        <h3>Panen Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Panen</th>
                    <th>Tanggal</th>
                    <th>Jenis Ikan</th>
                    <th>Harga Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_panen)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_panen']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_panen']); ?></td>
                        <td><?php echo htmlspecialchars($row['jenis_bibit']); ?></td>
                        <td>Rp <?php echo number_format($row['harga_total'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="summary-box">
        <h3>Total Biaya</h3>
        <ul>
            <li>Total Biaya Pakan: Rp <?php echo number_format($total_biaya_pakan, 2, ',', '.'); ?></li>
            <li>Total Biaya Pendukung: Rp <?php echo number_format($total_biaya_pendukung, 2, ',', '.'); ?></li>
            <li>Total Keseluruhan: Rp <?php echo number_format($total_biaya_pakan + $total_biaya_pendukung, 2, ',', '.'); ?></li>
        </ul>
    </div>
</div>

<!-- <div class="dashboard-actions">
    <h3>Aksi Cepat</h3>
    <button onclick="window.location.href='dashboard-user.php?page=input-data-panen'">Tambah Data Panen</button>
    <button onclick="window.location.href='dashboard-user.php?page=input-data-pemeliharaan-ikan'">Tambah Data Pemeliharaan</button>
    <button onclick="window.location.href='dashboard-user.php?page=input-data-biaya-pakan'">Tambah Data Biaya Pakan</button>
    <button onclick="window.location.href='dashboard-user.php?page=input-data-biaya-pendukung'">Tambah Data Biaya Pendukung</button>
</div> -->