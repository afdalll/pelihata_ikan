<?php

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan koneksi database sudah dibuat
if (!isset($conn)) {
    include_once 'includes/db.php';
}

$user_id = $_SESSION['user_id'];

// Query untuk mengambil data kolam sesuai dengan user_id
$query = "SELECT id_kolam, jenis_kolam, kapasitas_ikan, tanggal_penambahan FROM data_kolam WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $user_id);
$result = mysqli_stmt_execute($stmt);

if (!$result) {
    die("Execute failed: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);

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

<h2>Data Kolam</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data kolam...">
        <button id="search-button">Cari</button>
        <button id="add-data-button" onclick="window.location.href='dashboard-user.php?page=input-data-kolam'">Tambah Kolam</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Kolam</th>
                    <th>Jenis Kolam</th>
                    <th>Kapasitas Ikan</th>
                    <th>Tanggal Penambahan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['id_kolam'] . "</td>";
                        echo "<td>" . $row['jenis_kolam'] . "</td>";
                        echo "<td>" . $row['kapasitas_ikan'] . "</td>";
                        echo "<td>" . $row['tanggal_penambahan'] . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-user.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-kolam">
                                <input type="hidden" name="id" value="' . $row['id_kolam'] . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <button type="button" class="delete-button" 
                                    data-id="' . $row['id_kolam'] . '" 
                                    data-table="data_kolam" 
                                    data-return-page="data-kolam">Delete</button>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data kolam</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>