<?php

$user_id = $_SESSION['user_id'];

function generateBiayaPendukungId($conn)
{
    $query = "SELECT id_biaya_pendukung FROM biaya_pendukung ORDER BY id_biaya_pendukung DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_biaya_pendukung'];
        $newIdNumber = (int) substr($lastId, 3) + 1;
        $newId = 'BPG' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'BPG001';
    }
    return $newId;
}

// Query untuk mengambil data pemeliharaan ikan yang belum memiliki biaya pendukung
$query_pemeliharaan = "SELECT pi.id_pemeliharaan, pi.id_ikan, pi.id_kolam, pi.tanggal_tebar 
                       FROM pemeliharaan_ikan pi
                       LEFT JOIN biaya_pendukung bp ON pi.id_pemeliharaan = bp.id_pemeliharaan
                       WHERE pi.user_id = ? AND bp.id_biaya_pendukung IS NULL";
$stmt_pemeliharaan = mysqli_prepare($conn, $query_pemeliharaan);
mysqli_stmt_bind_param($stmt_pemeliharaan, "i", $user_id);
mysqli_stmt_execute($stmt_pemeliharaan);
$result_pemeliharaan = mysqli_stmt_get_result($stmt_pemeliharaan);

$pemeliharaan_options = [];
while ($row = mysqli_fetch_assoc($result_pemeliharaan)) {
    $pemeliharaan_options[$row['id_pemeliharaan']] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemeliharaan = filter_input(INPUT_POST, 'id_pemeliharaan', FILTER_SANITIZE_STRING);
    $obat = filter_input(INPUT_POST, 'obat', FILTER_VALIDATE_FLOAT);
    $suplemen = filter_input(INPUT_POST, 'suplemen', FILTER_VALIDATE_FLOAT);
    $lainnya = filter_input(INPUT_POST, 'lainnya', FILTER_VALIDATE_FLOAT);

    if ($id_pemeliharaan && $obat !== false && $suplemen !== false && $lainnya !== false) {
        $id_biaya_pendukung = generateBiayaPendukungId($conn);
        $pemeliharaan_data = $pemeliharaan_options[$id_pemeliharaan];
        
        $id_ikan = $pemeliharaan_data['id_ikan'];
        $id_kolam = $pemeliharaan_data['id_kolam'];
        $tanggal_tebar = $pemeliharaan_data['tanggal_tebar'];
        
        // Hitung total biaya pendukung
        $total_biaya_pendukung = $obat + $suplemen + $lainnya;

        // Query INSERT
        $query = "INSERT INTO biaya_pendukung (id_biaya_pendukung, user_id, id_ikan, id_pemeliharaan, tanggal_tebar, id_kolam, obat, suplemen, lainnya, total_biaya_pendukung) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Bind parameter
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssdddd", $id_biaya_pendukung, $user_id, $id_ikan, $id_pemeliharaan, $tanggal_tebar, $id_kolam, $obat, $suplemen, $lainnya, $total_biaya_pendukung);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data biaya pendukung berhasil ditambahkan.";
            header('Location: dashboard-user.php?page=biaya-pendukung');
            exit();
        } else {
            $error_message = "Gagal menambahkan data biaya pendukung: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Tambah Biaya Pendukung</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="id_pemeliharaan">ID Pemeliharaan:</label>
    <select name="id_pemeliharaan" id="id_pemeliharaan" required>
        <option value="">Pilih ID Pemeliharaan</option>
        <?php foreach ($pemeliharaan_options as $id_pemeliharaan => $pemeliharaan_data) : ?>
            <option value="<?php echo $id_pemeliharaan; ?>">
                <?php echo $id_pemeliharaan . ' (Kolam: ' . $pemeliharaan_data['id_kolam'] . ', Ikan: ' . $pemeliharaan_data['id_ikan'] . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (empty($pemeliharaan_options)) : ?>
        <p style="color: red;">Tidak ada data pemeliharaan ikan yang tersedia untuk menambahkan biaya pendukung.</p>
    <?php endif; ?>
    <br>
    <label for="obat">Biaya Obat (Rp):</label>
    <input type="number" name="obat" id="obat" step="0.01" min="0" required>
    <br>
    <label for="suplemen">Biaya Suplemen (Rp):</label>
    <input type="number" name="suplemen" id="suplemen" step="0.01" min="0" required>
    <br>
    <label for="lainnya">Biaya Lainnya (Rp):</label>
    <input type="number" name="lainnya" id="lainnya" step="0.01" min="0" required>
    <br>
    <button type="submit" <?php echo empty($pemeliharaan_options) ? 'disabled' : ''; ?>>Tambah</button>
    <a href="dashboard-user.php?page=biaya-pendukung" class="button">Kembali</a>
</form>