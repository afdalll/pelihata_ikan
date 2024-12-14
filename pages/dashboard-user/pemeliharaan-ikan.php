<?php

// Fungsi untuk memperbarui usia_pemeliharaan
function updateUsiaPemeliharaan($conn, $id_pemeliharaan, $usia_pemeliharaan) {
    $query = "UPDATE pemeliharaan_ikan SET usia_pemeliharaan = ? WHERE id_pemeliharaan = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $usia_pemeliharaan, $id_pemeliharaan);
    mysqli_stmt_execute($stmt);
}

$user_id = $_SESSION['user_id'];

// Query untuk mengambil data pemeliharaan ikan dengan JOIN
$query = "SELECT pi.*, dp.nama_pakan AS nama_pakan_aktual, tb.tanggal_tebar, tb.jumlah_bibit AS jumlah_awal_aktual
          FROM pemeliharaan_ikan pi
          LEFT JOIN data_pakan dp ON pi.nama_pakan = dp.nama_pakan AND pi.user_id = dp.user_id
          LEFT JOIN tebar_bibit tb ON pi.id_ikan = tb.id_ikan AND pi.id_kolam = tb.id_kolam AND pi.user_id = tb.user_id
          WHERE pi.user_id = ?
          ORDER BY pi.id_ikan, pi.usia_pemberian_pakan_awal";

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

<h2>Data Pemeliharaan Ikan</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data Pemeliharaan Ikan...">
        <button id="search-button">Cari</button>
        <button id="add-data-button" onclick="window.location.href='dashboard-user.php?page=input-data-Pemeliharaan-Ikan'">Tambah Data Pemeliharaan Ikan</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Pemeliharaan</th>
                    <th>ID Ikan</th>
                    <th>ID Kolam</th>
                    <th>Tanggal Tebar</th>
                    <th>Jenis Bibit</th>
                    <th>Nama Pakan</th>
                    <th>Usia pemberian pakan</th>
                    <th>Usia pemeliharaan</th>
                    <th>Pakan Harian</th>
                    <th>Jumlah Awal</th>
                    <th>Jumlah mati</th>
                    <th>Jumlah Hidup</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $prev_id_ikan = null;
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_pemeliharaan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_tebar']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_bibit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_pakan_aktual'] ?? $row['nama_pakan']) . "</td>";
                        
                        // Manipulasi tampilan Usia pemberian pakan
                        if ($row['id_ikan'] === $prev_id_ikan) {
                            echo "<td>" . htmlspecialchars($row['usia_pemberian_pakan_awal']) . "-" . htmlspecialchars($row['usia_pemberian_pakan_akhir']) . " hari</td>";
                        } else {
                            echo "<td>1-" . htmlspecialchars($row['usia_pemberian_pakan_akhir']) . " hari</td>";
                        }
                        
                        // Hitung usia pemeliharaan secara dinamis
                        $tanggal_tebar = new DateTime($row['tanggal_tebar']);
                        $tanggal_sekarang = new DateTime();
                        $usia_pemeliharaan_aktual = $tanggal_sekarang->diff($tanggal_tebar)->days;

                        // Jika usia pemeliharaan aktual berbeda dengan yang tersimpan, update database
                        if ($usia_pemeliharaan_aktual != $row['usia_pemeliharaan']) {
                            updateUsiaPemeliharaan($conn, $row['id_pemeliharaan'], $usia_pemeliharaan_aktual);
                        }

                        echo "<td>" . htmlspecialchars($usia_pemeliharaan_aktual) . " hari</td>";
                        
                        echo "<td>" . htmlspecialchars($row['pakan_harian']) . " kg</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_awal']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_mati']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_hidup']) . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-user.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-pemeliharaan-ikan">
                                <input type="hidden" name="id" value="' . $row['id_pemeliharaan'] . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <form method="POST" action="includes/delete_data.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . $row['id_pemeliharaan'] . '">
                                <input type="hidden" name="table" value="pemeliharaan_ikan">
                                <input type="hidden" name="return_page" value="pemeliharaan-ikan">
                                <button type="button" class="delete-button" data-id="' . $row['id_pemeliharaan'] . '" data-table="pemeliharaan_ikan" data-return-page="pemeliharaan-ikan">Delete</button>
                            </form>
                        </td>';
                        echo "</tr>";
                        
                        $prev_id_ikan = $row['id_ikan'];
                    }
                } else {
                    echo "<tr><td colspan='13'>Tidak ada data pemeliharaan ikan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>