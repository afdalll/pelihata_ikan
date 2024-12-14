<?php

$id_pakan = $_GET['id'];

// Ambil data pakan yang akan diedit
$query = "SELECT * FROM data_pakan WHERE id_pakan = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $id_pakan, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?page=data-pakan');
    exit();
}

$pakan_data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis_ikan = filter_input(INPUT_POST, 'jenis_ikan', FILTER_SANITIZE_STRING);
    $jenis_pakan = filter_input(INPUT_POST, 'jenis_pakan', FILTER_SANITIZE_STRING);
    $nama_pakan = filter_input(INPUT_POST, 'nama_pakan', FILTER_SANITIZE_STRING);
    $harga_per_kilo = filter_input(INPUT_POST, 'harga_per_kilo', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $deskripsi = filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_STRING);

    if ($jenis_ikan && $jenis_pakan && $nama_pakan && $harga_per_kilo && $deskripsi) {
        mysqli_begin_transaction($conn);

        try {
            // Update data_pakan
            $update_query = "UPDATE data_pakan SET jenis_ikan = ?, jenis_pakan = ?, nama_pakan = ?, harga_per_kilo = ?, deskripsi = ? WHERE id_pakan = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "sssdssi", $jenis_ikan, $jenis_pakan, $nama_pakan, $harga_per_kilo, $deskripsi, $id_pakan, $user_id);
            mysqli_stmt_execute($stmt);

            // Update pemeliharaan_ikan jika nama_pakan berubah
            if ($nama_pakan != $pakan_data['nama_pakan']) {
                $update_pemeliharaan_query = "UPDATE pemeliharaan_ikan SET nama_pakan = ? WHERE nama_pakan = ? AND user_id = ?";
                $stmt_pemeliharaan = mysqli_prepare($conn, $update_pemeliharaan_query);
                mysqli_stmt_bind_param($stmt_pemeliharaan, "ssi", $nama_pakan, $pakan_data['nama_pakan'], $user_id);
                mysqli_stmt_execute($stmt_pemeliharaan);
            }

            // Update biaya_pakan jika harga_per_kilo berubah
            if ($harga_per_kilo != $pakan_data['harga_per_kilo']) {
                $update_biaya_pakan_query = "UPDATE biaya_pakan bp
                                             JOIN pemeliharaan_ikan pi ON bp.id_pemeliharaan = pi.id_pemeliharaan
                                             SET bp.total_biaya_pakan = bp.total_digunakan * ?
                                             WHERE pi.nama_pakan = ? AND bp.user_id = ?";
                $stmt_biaya_pakan = mysqli_prepare($conn, $update_biaya_pakan_query);
                mysqli_stmt_bind_param($stmt_biaya_pakan, "dsi", $harga_per_kilo, $nama_pakan, $user_id);
                mysqli_stmt_execute($stmt_biaya_pakan);
            }

            mysqli_commit($conn);
            $_SESSION['success_message'] = "Data pakan berhasil diperbarui.";
            header('Location: dashboard-user.php?page=data-pakan');
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error_message = "Gagal memperbarui data pakan: " . $e->getMessage();
        }
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Pakan</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <p>ID Pakan: <?php echo htmlspecialchars($id_pakan); ?></p>
    <label for="jenis_ikan">Jenis Ikan:</label>
    <select name="jenis_ikan" id="jenis_ikan" required>
        <option value="">Pilih Jenis Ikan</option>
        <option value="Ikan Nila" <?php echo ($pakan_data['jenis_ikan'] == 'Ikan Nila') ? 'selected' : ''; ?>>Ikan Nila</option>
        <option value="Ikan Lele" <?php echo ($pakan_data['jenis_ikan'] == 'Ikan Lele') ? 'selected' : ''; ?>>Ikan Lele</option>
        <option value="Ikan Mas" <?php echo ($pakan_data['jenis_ikan'] == 'Ikan Mas') ? 'selected' : ''; ?>>Ikan Mas</option>
    </select>
    <br>
    <label for="jenis_pakan">Jenis Pakan:</label>
    <select name="jenis_pakan" id="jenis_pakan" required>
        <option value="">Pilih Jenis Pakan</option>
        <option value="Pakan Alami" <?php echo ($pakan_data['jenis_pakan'] == 'Pakan Alami') ? 'selected' : ''; ?>>Pakan Alami</option>
        <option value="Pakan Buatan" <?php echo ($pakan_data['jenis_pakan'] == 'Pakan Buatan') ? 'selected' : ''; ?>>Pakan Buatan</option>
    </select>
    <br>
    <label for="nama_pakan">Nama Pakan:</label>
    <input type="text" name="nama_pakan" id="nama_pakan" required value="<?php echo htmlspecialchars($pakan_data['nama_pakan']); ?>">
    <br>
    <label for="harga_per_kilo">Harga/Kilo:</label>
    <div class="input-group input-group-harga">
        <span class="input-group-addon">Rp</span>
        <input type="text" name="harga_per_kilo" id="harga_per_kilo" required pattern="[0-9]*\.?[0-9]+" title="Masukkan angka yang valid" value="<?php echo htmlspecialchars($pakan_data['harga_per_kilo']); ?>">
    </div>
    <br>
    <label for="deskripsi">Deskripsi:</label>
    <textarea name="deskripsi" id="deskripsi" required><?php echo htmlspecialchars($pakan_data['deskripsi']); ?></textarea>
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-user.php?page=data-pakan" class="button">Kembali</a>
</form>

<script>
document.getElementById('harga_per_kilo').addEventListener('input', function (e) {
    // Hapus semua karakter non-digit
    this.value = this.value.replace(/[^0-9.]/g, '');
    
    // Pastikan hanya ada satu titik desimal
    if ((this.value.match(/\./g) || []).length > 1) {
        this.value = this.value.replace(/\.+$/, '');
    }
});
</script>