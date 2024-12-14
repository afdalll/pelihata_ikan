<?php
$id_biaya_pendukung = $_GET['id'];

// Ambil data biaya pendukung yang akan diedit
$query = "SELECT * FROM biaya_pendukung WHERE id_biaya_pendukung = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $id_biaya_pendukung, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?page=biaya-pendukung');
    exit();
}

$biaya_pendukung = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $obat = filter_input(INPUT_POST, 'obat', FILTER_VALIDATE_FLOAT);
    $suplemen = filter_input(INPUT_POST, 'suplemen', FILTER_VALIDATE_FLOAT);
    $lainnya = filter_input(INPUT_POST, 'lainnya', FILTER_VALIDATE_FLOAT);

    if ($obat !== false && $suplemen !== false && $lainnya !== false) {
        // Hitung total biaya pendukung
        $total_biaya_pendukung = $obat + $suplemen + $lainnya;

        // Update data biaya pendukung
        $query = "UPDATE biaya_pendukung SET obat = ?, suplemen = ?, lainnya = ?, total_biaya_pendukung = ? WHERE id_biaya_pendukung = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ddddsi", $obat, $suplemen, $lainnya, $total_biaya_pendukung, $id_biaya_pendukung, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data biaya pendukung berhasil diperbarui.";
            header('Location: dashboard-user.php?page=biaya-pendukung');
            exit();
        } else {
            $error_message = "Gagal memperbarui data biaya pendukung: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Biaya Pendukung</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <p>ID Biaya Pendukung: <?php echo $id_biaya_pendukung; ?></p>
    <label for="obat">Biaya Obat (Rp):</label>
    <input type="number" name="obat" id="obat" step="0.01" required min="0" value="<?php echo htmlspecialchars($biaya_pendukung['obat']); ?>">
    <br>
    <label for="suplemen">Biaya Suplemen (Rp):</label>
    <input type="number" name="suplemen" id="suplemen" step="0.01" required min="0" value="<?php echo htmlspecialchars($biaya_pendukung['suplemen']); ?>">
    <br>
    <label for="lainnya">Biaya Lainnya (Rp):</label>
    <input type="number" name="lainnya" id="lainnya" step="0.01" required min="0" value="<?php echo htmlspecialchars($biaya_pendukung['lainnya']); ?>">
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-user.php?page=biaya-pendukung" class="button">Kembali</a>
</form>
