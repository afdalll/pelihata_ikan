<?php

$user_id = $_SESSION['user_id'];

function generateBiayaPakanId($conn)
{
    $query = "SELECT id_biaya_pakan FROM biaya_pakan ORDER BY id_biaya_pakan DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_biaya_pakan'];
        $newIdNumber = (int) substr($lastId, 2) + 1;
        $newId = 'BP' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'BP001';
    }
    return $newId;
}

// Query untuk mengambil data pemeliharaan ikan yang belum memiliki biaya pakan
$query_pemeliharaan = "SELECT pi.id_pemeliharaan, pi.id_ikan, pi.id_kolam, pi.tanggal_tebar, pi.nama_pakan, pi.usia_pemberian_pakan_awal, pi.usia_pemberian_pakan_akhir, pi.pakan_harian, dp.jenis_ikan, dp.harga_per_kilo
                       FROM pemeliharaan_ikan pi
                       LEFT JOIN data_pakan dp ON pi.nama_pakan = dp.nama_pakan
                       LEFT JOIN biaya_pakan bp ON pi.id_pemeliharaan = bp.id_pemeliharaan
                       WHERE pi.user_id = ? AND bp.id_biaya_pakan IS NULL";
$stmt_pemeliharaan = mysqli_prepare($conn, $query_pemeliharaan);
mysqli_stmt_bind_param($stmt_pemeliharaan, "i", $user_id);
mysqli_stmt_execute($stmt_pemeliharaan);
$result_pemeliharaan = mysqli_stmt_get_result($stmt_pemeliharaan);

$pemeliharaan_options = [];
while ($row = mysqli_fetch_assoc($result_pemeliharaan)) {
    $pemeliharaan_options[$row['id_pemeliharaan']] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemeliharaan = filter_input(INPUT_POST, 'id_pemeliharaan', FILTER_SANITIZE_STRING);

    if ($id_pemeliharaan) {
        $id_biaya_pakan = generateBiayaPakanId($conn);
        $pemeliharaan_data = $pemeliharaan_options[$id_pemeliharaan];
        
        $id_ikan = $pemeliharaan_data['id_ikan'];
        $id_kolam = $pemeliharaan_data['id_kolam'];
        $tanggal_tebar = $pemeliharaan_data['tanggal_tebar'];
        $nama_pakan = $pemeliharaan_data['nama_pakan'];
        $jenis_ikan = $pemeliharaan_data['jenis_ikan'];
        
        // Hitung total digunakan
        $usia_awal = $pemeliharaan_data['usia_pemberian_pakan_awal'];
        $usia_akhir = $pemeliharaan_data['usia_pemberian_pakan_akhir'];
        $pakan_harian = $pemeliharaan_data['pakan_harian'];
        $total_digunakan = ($usia_akhir - $usia_awal + 1) * $pakan_harian;
        
        // Hitung total biaya pakan
        $harga_per_kilo = $pemeliharaan_data['harga_per_kilo'];
        $total_biaya_pakan = $total_digunakan * $harga_per_kilo;

        // Query INSERT
        $query = "INSERT INTO biaya_pakan (id_biaya_pakan, user_id, id_ikan, id_pemeliharaan, id_kolam, tanggal_tebar, nama_pakan, jenis_ikan, total_digunakan, total_biaya_pakan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Bind parameter
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssdd", $id_biaya_pakan, $user_id, $id_ikan, $id_pemeliharaan, $id_kolam, $tanggal_tebar, $nama_pakan, $jenis_ikan, $total_digunakan, $total_biaya_pakan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data biaya pakan berhasil ditambahkan.";
            header('Location: dashboard-user.php?page=biaya-pakan');
            exit();
        } else {
            $error_message = "Gagal menambahkan data biaya pakan: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon pilih ID Pemeliharaan.";
    }
}
?>

<h2>Tambah Biaya Pakan</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="id_pemeliharaan">ID Pemeliharaan:</label>
    <select name="id_pemeliharaan" id="id_pemeliharaan" required>
        <option value="">Pilih ID Pemeliharaan</option>
        <?php foreach ($pemeliharaan_options as $id_pemeliharaan => $pemeliharaan_data) : ?>
            <option value="<?php echo $id_pemeliharaan; ?>">
                <?php echo $id_pemeliharaan . ' - ' . $pemeliharaan_data['nama_pakan'] . ' (Kolam: ' . $pemeliharaan_data['id_kolam'] . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>
    <button type="submit">Tambah</button>
    <a href="dashboard-user.php?page=biaya-pakan" class="button">Kembali</a>
</form>