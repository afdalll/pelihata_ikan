<?php
$id_panen = $_GET['id'];

// Ambil data panen yang akan diedit
$query = "SELECT * FROM panen WHERE id_panen = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $id_panen, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?page=panen');
    exit();
}

$data_panen = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_panen = filter_input(INPUT_POST, 'tanggal_panen', FILTER_SANITIZE_STRING);
    $harga_per_kg = filter_input(INPUT_POST, 'harga_per_kg', FILTER_VALIDATE_FLOAT);
    $berat_total = filter_input(INPUT_POST, 'berat_total', FILTER_VALIDATE_FLOAT);

    if ($tanggal_panen && $harga_per_kg !== false && $berat_total !== false) {
        $harga_total = $harga_per_kg * $berat_total;

        // Update data panen
        $query = "UPDATE panen SET tanggal_panen = ?, harga_per_kg = ?, berat_total = ?, harga_total = ? WHERE id_panen = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sdddsi", $tanggal_panen, $harga_per_kg, $berat_total, $harga_total, $id_panen, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data panen berhasil diperbarui.";
            header('Location: dashboard-user.php?page=panen');
            exit();
        } else {
            $error_message = "Gagal memperbarui data panen: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Panen</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <p>ID Panen: <?php echo $id_panen; ?></p>
    <label for="tanggal_panen">Tanggal Panen:</label>
    <input type="date" name="tanggal_panen" id="tanggal_panen" required value="<?php echo htmlspecialchars($data_panen['tanggal_panen']); ?>">
    <br>
    <label for="harga_per_kg">Harga per/kg (Rp):</label>
    <input type="number" name="harga_per_kg" id="harga_per_kg" step="0.01" required min="0" value="<?php echo htmlspecialchars($data_panen['harga_per_kg']); ?>">
    <br>
    <label for="berat_total">Berat Total Ikan (kg):</label>
    <input type="number" name="berat_total" id="berat_total" step="0.01" required min="0" value="<?php echo htmlspecialchars($data_panen['berat_total']); ?>">
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-user.php?page=panen" class="button">Kembali</a>
</form>
