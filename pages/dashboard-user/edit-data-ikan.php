<?php
$id_ikan = $_GET['id'];

// Ambil data ikan yang akan diedit
$query = "SELECT * FROM data_ikan WHERE id_ikan = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $id_ikan, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?page=data-ikan');
    exit();
}

$ikan_data = mysqli_fetch_assoc($result);

// Ambil data kolam yang tersedia
$query_kolam = "SELECT id_kolam, kapasitas_ikan FROM data_kolam WHERE user_id = ? AND (id_kolam = ? OR id_kolam NOT IN (SELECT id_kolam FROM data_ikan WHERE user_id = ?))";
$stmt_kolam = mysqli_prepare($conn, $query_kolam);
mysqli_stmt_bind_param($stmt_kolam, "isi", $user_id, $ikan_data['id_kolam'], $user_id);
mysqli_stmt_execute($stmt_kolam);
$result_kolam = mysqli_stmt_get_result($stmt_kolam);

$kolam_options = [];
while ($row = mysqli_fetch_assoc($result_kolam)) {
    $kolam_options[$row['id_kolam']] = $row['kapasitas_ikan'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_bibit = filter_input(INPUT_POST, 'jenis_bibit', FILTER_SANITIZE_STRING);
    $id_kolam = filter_input(INPUT_POST, 'id_kolam', FILTER_SANITIZE_STRING);
    $jumlah_bibit = filter_input(INPUT_POST, 'jumlah_bibit', FILTER_VALIDATE_INT);
    $tanggal_masuk_bibit = filter_input(INPUT_POST, 'tanggal_masuk_bibit', FILTER_SANITIZE_STRING);

    if ($jenis_bibit && $id_kolam && $jumlah_bibit && $tanggal_masuk_bibit) {
        if ($jumlah_bibit <= $kolam_options[$id_kolam]) {
            mysqli_begin_transaction($conn);
            
            try {
                // Update data_ikan
                $query = "UPDATE data_ikan SET jenis_bibit = ?, id_kolam = ?, jumlah_bibit = ?, tanggal_masuk_bibit = ? WHERE id_ikan = ? AND user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssissi", $jenis_bibit, $id_kolam, $jumlah_bibit, $tanggal_masuk_bibit, $id_ikan, $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Update tebar_bibit
                $query_tebar = "UPDATE tebar_bibit 
                                SET id_kolam = ?, tanggal_tebar = ?, jumlah_bibit = ?
                                WHERE id_ikan = ?";
                $stmt_tebar = mysqli_prepare($conn, $query_tebar);
                mysqli_stmt_bind_param($stmt_tebar, "ssis", $id_kolam, $tanggal_masuk_bibit, $jumlah_bibit, $id_ikan);
                mysqli_stmt_execute($stmt_tebar);
                mysqli_stmt_close($stmt_tebar);

                mysqli_commit($conn);

                $_SESSION['success_message'] = "Data ikan dan tebar bibit berhasil diperbarui.";
                header('Location: dashboard-user.php?page=data-ikan');
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_message = "Gagal memperbarui data: " . $e->getMessage();
            }
        } else {
            $error_message = "Jumlah bibit melebihi maksimum kolam (" . $kolam_options[$id_kolam] . ")!";
        }
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Data Ikan</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <p>ID Ikan: <?php echo $id_ikan; ?></p>
    <label for="jenis_bibit">Jenis Bibit:</label>
    <select name="jenis_bibit" id="jenis_bibit" required>
        <option value="">Pilih Jenis Bibit</option>
        <option value="Ikan Nila" <?php echo ($ikan_data['jenis_bibit'] == 'Ikan Nila') ? 'selected' : ''; ?>>Ikan Nila</option>
        <option value="Ikan Lele" <?php echo ($ikan_data['jenis_bibit'] == 'Ikan Lele') ? 'selected' : ''; ?>>Ikan Lele</option>
        <option value="Ikan Mas" <?php echo ($ikan_data['jenis_bibit'] == 'Ikan Mas') ? 'selected' : ''; ?>>Ikan Mas</option>
    </select>
    <br>
    <label for="id_kolam">ID Kolam:</label>
    <select name="id_kolam" id="id_kolam" required onchange="updateMaxJumlahBibit()">
        <option value="">Pilih ID Kolam</option>
        <?php foreach ($kolam_options as $id_kolam => $kapasitas) : ?>
            <option value="<?php echo $id_kolam; ?>" data-kapasitas="<?php echo $kapasitas; ?>" <?php echo ($ikan_data['id_kolam'] == $id_kolam) ? 'selected' : ''; ?>><?php echo $id_kolam; ?></option>
        <?php endforeach; ?>
    </select>
    <br>
    <label for="jumlah_bibit">Jumlah Bibit:</label>
    <input type="number" name="jumlah_bibit" id="jumlah_bibit" required min="1" value="<?php echo $ikan_data['jumlah_bibit']; ?>">
    <br>
    <label for="tanggal_masuk_bibit">Tanggal Masuk Bibit:</label>
    <input type="date" name="tanggal_masuk_bibit" id="tanggal_masuk_bibit" required value="<?php echo $ikan_data['tanggal_masuk_bibit']; ?>">
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-user.php?page=data-ikan" class="button">Kembali</a>
</form>

<script>
function updateMaxJumlahBibit() {
    var select = document.getElementById('id_kolam');
    var input = document.getElementById('jumlah_bibit');
    var selectedOption = select.options[select.selectedIndex];
    var maxKapasitas = selectedOption.getAttribute('data-kapasitas');
    
    input.placeholder = "maksimal " + maxKapasitas;
    input.max = maxKapasitas;
}

document.addEventListener('DOMContentLoaded', updateMaxJumlahBibit);
</script>