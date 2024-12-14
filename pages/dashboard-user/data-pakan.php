<?php

$user_id = $_SESSION['user_id'];

// Query untuk mengambil data pakan sesuai dengan user_id
$query = "SELECT id_pakan, jenis_ikan, jenis_pakan, nama_pakan, harga_per_kilo, deskripsi FROM data_pakan WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
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

<h2>Data Pakan</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data pakan...">
        <button id="search-button">Cari</button>
        <button id="add-data-button" onclick="window.location.href='dashboard-user.php?page=input-data-pakan'">Tambah Pakan</button>
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
                        echo '<td>
                            <form method="GET" action="dashboard-user.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-pakan">
                                <input type="hidden" name="id" value="' . $row['id_pakan'] . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <form method="POST" action="includes/delete_data.php" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . $row['id_pakan'] . '">
                                <input type="hidden" name="table" value="data_pakan">
                                <input type="hidden" name="return_page" value="data-pakan">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data pakan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>