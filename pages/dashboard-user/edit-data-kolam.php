<?php
$id_kolam = $_GET['id'];

// Ambil data kolam yang akan diedit
$query = "SELECT * FROM data_kolam WHERE id_kolam = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $id_kolam, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?page=data-kolam');
    exit();
}

$kolam_data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_kolam = filter_input(INPUT_POST, 'jenis_kolam', FILTER_SANITIZE_STRING);
    $kapasitas_ikan = filter_input(INPUT_POST, 'kapasitas_ikan', FILTER_VALIDATE_INT);
    $tanggal_penambahan = filter_input(INPUT_POST, 'tanggal_penambahan', FILTER_SANITIZE_STRING);

    if ($jenis_kolam && $kapasitas_ikan && $tanggal_penambahan) {
        $query = "UPDATE data_kolam SET jenis_kolam = ?, kapasitas_ikan = ?, tanggal_penambahan = ? WHERE id_kolam = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sissi", $jenis_kolam, $kapasitas_ikan, $tanggal_penambahan, $id_kolam, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            // Update tebar_bibit
            $update_tebar_bibit = "UPDATE tebar_bibit tb
                                   JOIN data_ikan di ON tb.id_ikan = di.id_ikan
                                   SET tb.id_kolam = ?
                                   WHERE di.id_kolam = ? AND di.user_id = ?";
            $stmt_tebar_bibit = mysqli_prepare($conn, $update_tebar_bibit);
            mysqli_stmt_bind_param($stmt_tebar_bibit, "ssi", $id_kolam, $id_kolam, $user_id);
            mysqli_stmt_execute($stmt_tebar_bibit);
            mysqli_stmt_close($stmt_tebar_bibit);

            $_SESSION['success_message'] = "Data kolam dan tebar bibit berhasil diperbarui.";
            header('Location: dashboard-user.php?page=data-kolam');
            exit();
        } else {
            $error_message = "Gagal memperbarui data kolam: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
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
    <p>ID Kolam: <?php echo $id_kolam; ?></p>
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
    <input type="number" name="kapasitas_ikan" id="kapasitas_ikan" required min="1" value="<?php echo $kolam_data['kapasitas_ikan']; ?>">
    <br>
    <label for="tanggal_penambahan">Tanggal Penambahan:</label>
    <input type="date" name="tanggal_penambahan" id="tanggal_penambahan" required value="<?php echo $kolam_data['tanggal_penambahan']; ?>">
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-user.php?page=data-kolam" class="button">Kembali</a>
</form>