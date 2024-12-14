<?php

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi database sudah dibuat
if (!isset($conn)) {
    include_once 'includes/db.php';
}

// Query untuk mengambil semua data ikan
$query = "SELECT id_ikan, jenis_bibit, id_kolam, jumlah_bibit, tanggal_masuk_bibit, user_id FROM data_ikan";
$result = mysqli_query($conn, $query);

// Debugging: Periksa hasil query
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Tampilkan pesan sukses atau error
if (isset($_SESSION['delete_success'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['delete_success'] . "</div>";
    unset($_SESSION['delete_success']);
} elseif (isset($_SESSION['delete_error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['delete_error'] . "</div>";
    unset($_SESSION['delete_error']);
}
?>

<h2>Data Ikan</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data ikan...">
        <button id="search-button">Cari</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Ikan</th>
                    <th>Jenis Bibit</th>
                    <th>ID Kolam</th>
                    <th>Jumlah Bibit</th>
                    <th>Tanggal Masuk Bibit</th>
                    <th>User ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_bibit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_bibit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['tanggal_masuk_bibit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-admin.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-ikan">
                                <input type="hidden" name="id" value="' . htmlspecialchars($row['id_ikan']) . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <button type="button" class="delete-button" 
                                    data-id="' . htmlspecialchars($row['id_ikan']) . '" 
                                    data-table="data_ikan" 
                                    data-return-page="data-ikan">Delete</button>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data ikan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>