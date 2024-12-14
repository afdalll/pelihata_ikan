<?php

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi database sudah dibuat
if (!isset($conn)) {
    include_once 'includes/db.php';
}

// Query untuk mengambil semua data pakan
$query = "SELECT id_pakan, jenis_ikan, jenis_pakan, nama_pakan, harga_per_kilo, deskripsi, user_id FROM data_pakan";
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

<h2>Data Pakan</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data pakan...">
        <button id="search-button">Cari</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Pakan</th>
                    <th>Jenis Ikan</th>
                    <th>Jenis Pakan</th>
                    <th>Nama Pakan</th>
                    <th>Harga/Kilo</th>
                    <th>Deskripsi</th>
                    <th>User ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_pakan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_ikan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis_pakan']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_pakan']) . "</td>";
                        echo "<td>Rp " . number_format($row['harga_per_kilo'], 2, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-admin.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-pakan">
                                <input type="hidden" name="id" value="' . htmlspecialchars($row['id_pakan']) . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <button type="button" class="delete-button" 
                                    data-id="' . htmlspecialchars($row['id_pakan']) . '" 
                                    data-table="data_pakan" 
                                    data-return-page="data-pakan">Delete</button>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data pakan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
