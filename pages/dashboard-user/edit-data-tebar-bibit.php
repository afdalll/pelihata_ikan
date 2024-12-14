<?php
// Periksa apakah ada ID tebar bibit yang diberikan
if (!isset($_GET['id'])) {
    header('Location: dashboard.php?page=tebar-bibit');
    exit();
}

$id_tebar = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data tebar bibit yang akan diedit
$query = "SELECT * FROM tebar_bibit WHERE id_tebar = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $id_tebar, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?page=tebar-bibit');
    exit();
}

$tebar_bibit = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ukuran = filter_input(INPUT_POST, 'ukuran', FILTER_VALIDATE_FLOAT);
    $harga_bibit_satuan = filter_input(INPUT_POST, 'harga_bibit_satuan', FILTER_VALIDATE_FLOAT);

    if ($ukuran && $harga_bibit_satuan) {
        $query = "UPDATE tebar_bibit SET ukuran = ?, harga_bibit_satuan = ? WHERE id_tebar = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ddsi", $ukuran, $harga_bibit_satuan, $id_tebar, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data tebar bibit berhasil diperbarui.";
            header('Location: dashboard-user.php?page=tebar-bibit');
            exit();
        } else {
            $error_message = "Gagal memperbarui data tebar bibit: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Tebar Bibit</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <p>ID Tebar: <?php echo htmlspecialchars($id_tebar); ?></p>
    <label for="ukuran">Ukuran (cm):</label>
    <input type="number" name="ukuran" id="ukuran" step="0.1" required min="0" value="<?php echo htmlspecialchars($tebar_bibit['ukuran']); ?>">
    <br>
    <label for="harga_bibit_satuan">Harga Bibit Satuan (Rp):</label>
    <input type="number" name="harga_bibit_satuan" id="harga_bibit_satuan" step="0.01" required min="0" value="<?php echo htmlspecialchars($tebar_bibit['harga_bibit_satuan']); ?>">
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-user.php?page=tebar-bibit" class="button">Kembali</a>
</form>