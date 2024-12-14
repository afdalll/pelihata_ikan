<?php

$user_id = $_SESSION['user_id'];

function generateTebarId($conn)
{
    $query = "SELECT id_tebar FROM tebar_bibit ORDER BY id_tebar DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_tebar'];
        $newIdNumber = (int) substr($lastId, 3) + 1;
        $newId = 'TBR' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'TBR001';
    }
    return $newId;
}

// Ambil data ikan yang tersedia dan belum digunakan di tebar_bibit
$query_ikan = "SELECT di.id_ikan, di.jenis_bibit, di.id_kolam, di.jumlah_bibit, di.tanggal_masuk_bibit 
               FROM data_ikan di
               LEFT JOIN tebar_bibit tb ON di.id_ikan = tb.id_ikan
               WHERE di.user_id = ? AND tb.id_ikan IS NULL";
$stmt_ikan = mysqli_prepare($conn, $query_ikan);
mysqli_stmt_bind_param($stmt_ikan, "i", $user_id);
mysqli_stmt_execute($stmt_ikan);
$result_ikan = mysqli_stmt_get_result($stmt_ikan);

$ikan_options = [];
while ($row = mysqli_fetch_assoc($result_ikan)) {
    $ikan_options[$row['id_ikan']] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($ikan_options)) {
        $error_message = "Tidak ada data ikan yang tersedia untuk tebar bibit.";
    } else {
        $id_ikan = filter_input(INPUT_POST, 'id_ikan', FILTER_SANITIZE_STRING);
        $ukuran = filter_input(INPUT_POST, 'ukuran', FILTER_VALIDATE_FLOAT);
        $harga_bibit_satuan = filter_input(INPUT_POST, 'harga_bibit_satuan', FILTER_VALIDATE_FLOAT);

        if ($id_ikan && $ukuran && $harga_bibit_satuan) {
            $id_tebar = generateTebarId($conn);
            $ikan_data = $ikan_options[$id_ikan];
            $id_kolam = $ikan_data['id_kolam'];
            $tanggal_tebar = $ikan_data['tanggal_masuk_bibit'];
            $jumlah_bibit = $ikan_data['jumlah_bibit'];

            $query = "INSERT INTO tebar_bibit (id_tebar, user_id, id_ikan, id_kolam, tanggal_tebar, jumlah_bibit, ukuran, harga_bibit_satuan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssidd", $id_tebar, $user_id, $id_ikan, $id_kolam, $tanggal_tebar, $jumlah_bibit, $ukuran, $harga_bibit_satuan);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Data tebar bibit berhasil ditambahkan.";
                header('Location: dashboard-user.php?page=tebar-bibit');
                exit();
            } else {
                $error_message = "Gagal menambahkan data tebar bibit: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Mohon isi semua field dengan benar.";
        }
    }
}
?>

<h2>Tambah Tebar Bibit</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="id_ikan">ID Ikan:</label>
    <select name="id_ikan" id="id_ikan" required>
        <option value="">Pilih ID Ikan</option>
        <?php foreach ($ikan_options as $id_ikan => $ikan_data) : ?>
            <option value="<?php echo $id_ikan; ?>"><?php echo $id_ikan . ' - ' . $ikan_data['jenis_bibit'] . ' (Kolam: ' . $ikan_data['id_kolam'] . ')'; ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (empty($ikan_options)) : ?>
        <p style="color: red;">Tidak ada data ikan yang tersedia untuk tebar bibit.</p>
    <?php endif; ?>
    <br>
    <label for="ukuran">Ukuran (cm):</label>
    <input type="number" name="ukuran" id="ukuran" step="0.1" required min="0">
    <br>
    <label for="harga_bibit_satuan">Harga Bibit Satuan (Rp):</label>
    <input type="number" name="harga_bibit_satuan" id="harga_bibit_satuan" step="0.01" required min="0">
    <br>
    <button type="submit">Tambah</button>
    <a href="dashboard-user.php?page=tebar-bibit" class="button">Kembali</a>
</form>