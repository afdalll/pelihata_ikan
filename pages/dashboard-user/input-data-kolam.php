<?php

$user_id = $_SESSION['user_id'];

function generateKolamId($conn)
{
    $query = "SELECT id_kolam FROM data_kolam ORDER BY id_kolam DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_kolam'];
        $newIdNumber = (int) substr($lastId, 3) + 1;
        $newId = 'KOL' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'KOL001';
    }
    return $newId;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_kolam = filter_input(INPUT_POST, 'jenis_kolam', FILTER_SANITIZE_STRING);
    $kapasitas_ikan = filter_input(INPUT_POST, 'kapasitas_ikan', FILTER_VALIDATE_INT);
    $tanggal_penambahan = filter_input(INPUT_POST, 'tanggal_penambahan', FILTER_SANITIZE_STRING);

    if ($jenis_kolam && $kapasitas_ikan && $tanggal_penambahan) {
        $id_kolam = generateKolamId($conn);
        $query = "INSERT INTO data_kolam (id_kolam, user_id, jenis_kolam, kapasitas_ikan, tanggal_penambahan) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssis", $id_kolam, $user_id, $jenis_kolam, $kapasitas_ikan, $tanggal_penambahan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data kolam berhasil ditambahkan.";
            header('Location: dashboard-user.php?page=data-kolam');
            exit();
        } else {
            $error_message = "Gagal menambahkan data kolam: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Tambah Data Kolam</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="jenis_kolam">Jenis Kolam:</label>
    <select name="jenis_kolam" id="jenis_kolam" required>
        <option value="">Pilih Jenis Kolam</option>
        <option value="Kolam Terpal">Kolam Terpal</option>
        <option value="Kolam Tanah">Kolam Tanah</option>
        <option value="Kolam Beton (Semen)">Kolam Beton (Semen)</option>
        <option value="Keramba (Jaring Apung)">Keramba (Jaring Apung)</option>
        <option value="Kolam Plastik">Kolam Plastik</option>
        <option value="Kolam Fiber">Kolam Fiber</option>
    </select>
    <br>
    <label for="kapasitas_ikan">Kapasitas Ikan:</label>
    <input type="number" name="kapasitas_ikan" id="kapasitas_ikan" required min="1">
    <br>
    <label for="tanggal_penambahan">Tanggal Penambahan:</label>
    <input type="date" name="tanggal_penambahan" id="tanggal_penambahan" required>
    <br>
    <button type="submit">Tambah</button>
    <a href="dashboard-user.php?page=data-kolam" class="button">Kembali</a>
</form>