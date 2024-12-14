<?php
$user_id = $_SESSION['user_id'];

// Query untuk mengambil data panen
$query = "SELECT DISTINCT p.id_panen, p.id_ikan, p.tanggal_panen, p.id_kolam, di.jenis_bibit AS jenis_ikan, 
          COALESCE(pi.usia_pemeliharaan, p.usia_pemeliharaan) AS usia_pemeliharaan, 
          p.harga_per_kg, p.berat_total, p.harga_total
          FROM panen p
          JOIN data_ikan di ON p.id_ikan = di.id_ikan
          LEFT JOIN pemeliharaan_ikan pi ON p.id_ikan = pi.id_ikan AND pi.user_id = p.user_id
          WHERE p.user_id = ?
          GROUP BY p.id_panen
          ORDER BY p.tanggal_panen DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Tampilkan pesan sukses atau error jika ada
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-" . $_SESSION['message_type'] . "'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<h2>Data Panen</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari data Panen...">
        <button id="search-button">Cari</button>
        <button id="add-data-button" onclick="window.location.href='dashboard-user.php?page=input-data-panen'">Tambah Data Panen</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID Panen</th>
                    <th>ID Ikan</th>
                    <th>Tanggal Panen</th>
                    <th>ID Kolam</th>
                    <th>Jenis Ikan</th>
                    <th>Usia Pemeliharaan</th>
                    <th>Harga per/kg</th>
                    <th>Berat Total ikan</th>
                    <th>Harga Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $displayed_panen = array(); // Array untuk melacak ID panen yang sudah ditampilkan
                    while ($row = mysqli_fetch_assoc($result)) {
                        if (!in_array($row['id_panen'], $displayed_panen)) {
                            $displayed_panen[] = $row['id_panen'];
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id_panen']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['id_ikan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tanggal_panen']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['id_kolam']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['jenis_ikan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['usia_pemeliharaan']) . " hari</td>";
                            echo "<td>Rp " . number_format($row['harga_per_kg'], 2, ',', '.') . "</td>";
                            echo "<td>" . number_format($row['berat_total'], 2, ',', '.') . " kg</td>";
                            echo "<td>Rp " . number_format($row['harga_total'], 2, ',', '.') . "</td>";
                            echo '<td>
                                <form method="GET" action="dashboard-user.php" style="display:inline;">
                                    <input type="hidden" name="page" value="edit-data-panen">
                                    <input type="hidden" name="id" value="' . $row['id_panen'] . '">
                                    <button type="submit" class="edit-button">Edit</button>
                                </form>
                                <form method="POST" action="includes/delete_data.php" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="' . $row['id_panen'] . '">
                                    <input type="hidden" name="table" value="panen">
                                    <input type="hidden" name="return_page" value="panen">
                                    <button type="button" class="delete-button" data-id="' . $row['id_panen'] . '" data-table="panen" data-return-page="panen">Delete</button>
                                </form>
                            </td>';
                            echo "</tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='10'>Tidak ada data panen</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>