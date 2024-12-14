<?php

$user_id = $_SESSION['user_id'];

// Query untuk mengambil data tebar bibit sesuai dengan user_id
$query = "SELECT tb.id_tebar, tb.id_ikan, tb.id_kolam, tb.tanggal_tebar, 
                 di.jenis_bibit, tb.jumlah_bibit, tb.ukuran, tb.harga_bibit_satuan 
          FROM tebar_bibit tb
          JOIN data_ikan di ON tb.id_ikan = di.id_ikan
          WHERE tb.user_id = ?
          ORDER BY tb.tanggal_tebar DESC";

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

<h2>Data Tebar Bibit</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data Tebar Bibit...">
        <button id="search-button">Cari</button>
        <button id="add-data-button" onclick="window.location.href='dashboard-user.php?page=input-data-tebar-bibit'">Tambah Data Tebar Bibit</button>
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
                        echo '<td>
                            <form method="GET" action="dashboard-user.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-tebar-bibit">
                                <input type="hidden" name="id" value="' . $row['id_tebar'] . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <form method="POST" action="includes/delete_data.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . $row['id_tebar'] . '">
                                <input type="hidden" name="table" value="tebar_bibit">
                                <input type="hidden" name="return_page" value="tebar-bibit">
                                <button type="button" class="delete-button" data-id="' . $row['id_tebar'] . '" data-table="tebar_bibit" data-return-page="tebar-bibit">Delete</button>
                            </form>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Tidak ada data tebar bibit</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>