<?php

$user_id = $_SESSION['user_id'];

function generatePakanId($conn)
{
    $query = "SELECT id_pakan FROM data_pakan ORDER BY id_pakan DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_pakan'];
        $newIdNumber = (int) substr($lastId, 3) + 1;
        $newId = 'PAK' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'PAK001';
    }
    return $newId;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pakan = generatePakanId($conn);
    $jenis_ikan = filter_input(INPUT_POST, 'jenis_ikan', FILTER_SANITIZE_STRING);
    $jenis_pakan = filter_input(INPUT_POST, 'jenis_pakan', FILTER_SANITIZE_STRING);
    $nama_pakan = filter_input(INPUT_POST, 'nama_pakan', FILTER_SANITIZE_STRING);
    $harga_per_kilo = filter_input(INPUT_POST, 'harga_per_kilo', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $deskripsi = filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_STRING);

    if ($jenis_ikan && $jenis_pakan && $nama_pakan && $harga_per_kilo && $deskripsi) {
        $query = "INSERT INTO data_pakan (id_pakan, user_id, jenis_ikan, jenis_pakan, nama_pakan, harga_per_kilo, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sisssds", $id_pakan, $user_id, $jenis_ikan, $jenis_pakan, $nama_pakan, $harga_per_kilo, $deskripsi);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data pakan berhasil ditambahkan.";
            header('Location: dashboard-user.php?page=data-pakan');
            exit();
        } else {
            $error_message = "Gagal menambahkan data pakan: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Tambah Data Pakan</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="jenis_ikan">Jenis Ikan:</label>
    <select name="jenis_ikan" id="jenis_ikan" required>
        <option value="">Pilih Jenis Ikan</option>
        <option value="Ikan Nila">Ikan Nila</option>
        <option value="Ikan Lele">Ikan Lele</option>
        <option value="Ikan Mas">Ikan Mas</option>
    </select>
    <br>
    <label for="jenis_pakan">Jenis Pakan:</label>
    <select name="jenis_pakan" id="jenis_pakan" required>
        <option value="">Pilih Jenis Pakan</option>
        <option value="Pakan Alami">Pakan Alami</option>
        <option value="Pakan Buatan">Pakan Buatan</option>
    </select>
    <br>
    <label for="nama_pakan">Nama Pakan:</label>
    <input type="text" name="nama_pakan" id="nama_pakan" required>
    <br>
    <label for="harga_per_kilo">Harga/Kilo:</label>
    <div class="input-group input-group-harga">
        <span class="input-group-addon">Rp</span>
        <input type="text" name="harga_per_kilo" id="harga_per_kilo" required pattern="[0-9]*\.?[0-9]+" title="Masukkan angka yang valid">
    </div>
    <br>
    <label for="deskripsi">Deskripsi:</label>
    <textarea name="deskripsi" id="deskripsi" required></textarea>
    <br>
    <button type="submit">Tambah</button>
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
