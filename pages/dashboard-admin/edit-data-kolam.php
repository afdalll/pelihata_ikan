<?php
$id_kolam = $_GET['id'];

// Ambil data kolam yang akan diedit (tanpa filter user_id karena admin)
$query = "SELECT dk.*, u.username 
          FROM data_kolam dk 
          LEFT JOIN user u ON dk.user_id = u.id 
          WHERE dk.id_kolam = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_kolam);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard-admin.php?page=data-kolam');
    exit();
}

$kolam_data = mysqli_fetch_assoc($result);

// Cek apakah kolam sedang digunakan
$query_check = "SELECT COUNT(*) as count FROM data_ikan WHERE id_kolam = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "s", $id_kolam);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$row_check = mysqli_fetch_assoc($result_check);
$is_used = $row_check['count'] > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_kolam = filter_input(INPUT_POST, 'jenis_kolam', FILTER_SANITIZE_STRING);
    $kapasitas_ikan = filter_input(INPUT_POST, 'kapasitas_ikan', FILTER_VALIDATE_INT);
    $tanggal_penambahan = filter_input(INPUT_POST, 'tanggal_penambahan', FILTER_SANITIZE_STRING);

    if ($jenis_kolam && $kapasitas_ikan && $tanggal_penambahan) {
        // Jika kolam digunakan, cek kapasitas tidak kurang dari jumlah ikan yang ada
        if ($is_used) {
            $query_ikan = "SELECT jumlah_bibit FROM data_ikan WHERE id_kolam = ?";
            $stmt_ikan = mysqli_prepare($conn, $query_ikan);
            mysqli_stmt_bind_param($stmt_ikan, "s", $id_kolam);
            mysqli_stmt_execute($stmt_ikan);
            $result_ikan = mysqli_stmt_get_result($stmt_ikan);
            $row_ikan = mysqli_fetch_assoc($result_ikan);
            
            if ($kapasitas_ikan < $row_ikan['jumlah_bibit']) {
                $error_message = "Kapasitas tidak boleh kurang dari jumlah ikan yang ada (" . $row_ikan['jumlah_bibit'] . ")";
            }
        }

        if (!isset($error_message)) {
            // Update data kolam (tanpa filter user_id)
            $query = "UPDATE data_kolam 
                     SET jenis_kolam = ?, 
                         kapasitas_ikan = ?, 
                         tanggal_penambahan = ? 
                     WHERE id_kolam = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "siss", $jenis_kolam, $kapasitas_ikan, $tanggal_penambahan, $id_kolam);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Data kolam berhasil diperbarui.";
                header('Location: dashboard-admin.php?page=data-kolam');
                exit();
            } else {
                $error_message = "Gagal memperbarui data: " . mysqli_error($conn);
            }
        }
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Data Kolam</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <p class="user-info">ID Kolam: <?php echo htmlspecialchars($id_kolam); ?> dari Username: <?php echo htmlspecialchars($kolam_data['username']); ?></p>
    
    <label for="jenis_kolam">Jenis Kolam:</label>
    <select name="jenis_kolam" id="jenis_kolam" required>
        <option value="">Pilih Jenis Kolam</option>
        <option value="Kolam Terpal" <?php echo ($kolam_data['jenis_kolam'] == 'Kolam Terpal') ? 'selected' : ''; ?>>Kolam Terpal</option>
        <option value="Kolam Tanah" <?php echo ($kolam_data['jenis_kolam'] == 'Kolam Tanah') ? 'selected' : ''; ?>>Kolam Tanah</option>
        <option value="Kolam Beton (Semen)" <?php echo ($kolam_data['jenis_kolam'] == 'Kolam Beton (Semen)') ? 'selected' : ''; ?>>Kolam Beton (Semen)</option>
        <option value="Keramba (Jaring Apung)" <?php echo ($kolam_data['jenis_kolam'] == 'Keramba (Jaring Apung)') ? 'selected' : ''; ?>>Keramba (Jaring Apung)</option>
        <option value="Kolam Plastik" <?php echo ($kolam_data['jenis_kolam'] == 'Kolam Plastik') ? 'selected' : ''; ?>>Kolam Plastik</option>
        <option value="Kolam Fiber" <?php echo ($kolam_data['jenis_kolam'] == 'Kolam Fiber') ? 'selected' : ''; ?>>Kolam Fiber</option>
    </select>
    <br>
    <label for="kapasitas_ikan">Kapasitas Ikan:</label>
    <input type="number" name="kapasitas_ikan" id="kapasitas_ikan" required min="1" value="<?php echo $kolam_data['kapasitas_ikan']; ?>" <?php echo $is_used ? 'min="' . $row_check['count'] . '"' : ''; ?>>
    <br>
    <label for="tanggal_penambahan">Tanggal Penambahan:</label>
    <input type="date" name="tanggal_penambahan" id="tanggal_penambahan" required value="<?php echo $kolam_data['tanggal_penambahan']; ?>">
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-admin.php?page=data-kolam" class="button">Kembali</a>
</form>