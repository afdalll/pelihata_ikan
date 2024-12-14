<?php

$user_id = $_SESSION['user_id'];

// Query untuk mengambil data ikan sesuai dengan user_id
$query = "SELECT id_ikan, jenis_bibit, id_kolam, jumlah_bibit, tanggal_masuk_bibit FROM data_ikan WHERE user_id = $user_id";
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
        <button id="add-data-button" onclick="window.location.href='dashboard-user.php?page=input-data-ikan'">Tambah Ikan</button>
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['id_ikan'] . "</td>";
                        echo "<td>" . $row['jenis_bibit'] . "</td>";
                        echo "<td>" . $row['id_kolam'] . "</td>";
                        echo "<td>" . $row['jumlah_bibit'] . "</td>";
                        echo "<td>" . $row['tanggal_masuk_bibit'] . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-user.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-data-ikan">
                                <input type="hidden" name="id" value="' . $row['id_ikan'] . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <button type="button" class="delete-button" data-id="' . $row['id_ikan'] . '" data-table="data_ikan" data-return-page="data-ikan">Delete</button>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data ikan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>