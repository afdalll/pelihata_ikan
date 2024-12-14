<?php

$user_id = $_SESSION['user_id'];

function generatePemeliharaanId($conn)
{
    $query = "SELECT id_pemeliharaan FROM pemeliharaan_ikan ORDER BY id_pemeliharaan DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_pemeliharaan'];
        $newIdNumber = (int) substr($lastId, 3) + 1;
        $newId = 'PMI' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'PMI001';
    }
    return $newId;
}

function getLastPemeliharaan($conn, $id_ikan, $user_id) {
    $query = "SELECT * FROM pemeliharaan_ikan 
              WHERE id_ikan = ? AND user_id = ? 
              ORDER BY usia_pemberian_pakan_akhir DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $id_ikan, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Ambil data tebar bibit yang tersedia
$query_tebar_bibit = "SELECT tb.id_ikan, tb.id_kolam, tb.tanggal_tebar, tb.jumlah_bibit, di.jenis_bibit 
                      FROM tebar_bibit tb
                      JOIN data_ikan di ON tb.id_ikan = di.id_ikan
                      WHERE tb.user_id = ?";
$stmt_tebar_bibit = mysqli_prepare($conn, $query_tebar_bibit);
mysqli_stmt_bind_param($stmt_tebar_bibit, "i", $user_id);
mysqli_stmt_execute($stmt_tebar_bibit);
$result_tebar_bibit = mysqli_stmt_get_result($stmt_tebar_bibit);

$tebar_bibit_options = [];
while ($row = mysqli_fetch_assoc($result_tebar_bibit)) {
    $tebar_bibit_options[$row['id_ikan']] = $row;
}

// Ambil data pakan
$query_pakan = "SELECT nama_pakan FROM data_pakan WHERE user_id = ?";
$stmt_pakan = mysqli_prepare($conn, $query_pakan);
mysqli_stmt_bind_param($stmt_pakan, "i", $user_id);
mysqli_stmt_execute($stmt_pakan);
$result_pakan = mysqli_stmt_get_result($stmt_pakan);

$pakan_options = [];
while ($row = mysqli_fetch_assoc($result_pakan)) {
    $pakan_options[] = $row['nama_pakan'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ikan = filter_input(INPUT_POST, 'id_ikan', FILTER_SANITIZE_STRING);
    $nama_pakan = filter_input(INPUT_POST, 'nama_pakan', FILTER_SANITIZE_STRING);
    $usia_pemberian_pakan = filter_input(INPUT_POST, 'usia_pemberian_pakan', FILTER_VALIDATE_INT);
    $pakan_harian = filter_input(INPUT_POST, 'pakan_harian', FILTER_VALIDATE_FLOAT);
    $jumlah_mati = filter_input(INPUT_POST, 'jumlah_mati', FILTER_VALIDATE_INT);

    if ($id_ikan && $nama_pakan && $usia_pemberian_pakan !== false && $pakan_harian !== false && $jumlah_mati !== false) {
        $id_pemeliharaan = generatePemeliharaanId($conn);
        $tebar_data = $tebar_bibit_options[$id_ikan];
        $id_kolam = $tebar_data['id_kolam'];
        $tanggal_tebar = $tebar_data['tanggal_tebar'];
        $jenis_bibit = $tebar_data['jenis_bibit'];
        
        // Ambil data pemeliharaan terakhir
        $last_pemeliharaan = getLastPemeliharaan($conn, $id_ikan, $user_id);
        
        if ($last_pemeliharaan) {
            $jumlah_awal = $last_pemeliharaan['jumlah_hidup'];
            $usia_pemberian_pakan_awal = $last_pemeliharaan['usia_pemberian_pakan_akhir'] + 1;
        } else {
            $jumlah_awal = $tebar_data['jumlah_bibit'];
            $usia_pemberian_pakan_awal = 1;
        }
        
        // Hitung usia pemeliharaan
        $tanggal_sekarang = new DateTime();
        $tanggal_tebar_obj = new DateTime($tanggal_tebar);
        $usia_pemeliharaan = $tanggal_sekarang->diff($tanggal_tebar_obj)->days;
        
        // Hitung jumlah hidup
        $jumlah_hidup = $jumlah_awal - $jumlah_mati;

        // Query INSERT
        $query = "INSERT INTO pemeliharaan_ikan (id_pemeliharaan, user_id, id_ikan, id_kolam, tanggal_tebar, jenis_bibit, nama_pakan, usia_pemberian_pakan_awal, usia_pemberian_pakan_akhir, usia_pemeliharaan, pakan_harian, jumlah_awal, jumlah_mati, jumlah_hidup) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Bind parameter
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssiiiidii", $id_pemeliharaan, $user_id, $id_ikan, $id_kolam, $tanggal_tebar, $jenis_bibit, $nama_pakan, $usia_pemberian_pakan_awal, $usia_pemberian_pakan, $usia_pemeliharaan, $pakan_harian, $jumlah_awal, $jumlah_mati, $jumlah_hidup);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data pemeliharaan ikan berhasil ditambahkan.";
            header('Location: dashboard-user.php?page=pemeliharaan-ikan');
            exit();
        } else {
            $error_message = "Gagal menambahkan data pemeliharaan ikan: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Tambah Pemeliharaan Ikan</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="id_ikan">ID Ikan:</label>
    <select name="id_ikan" id="id_ikan" required>
        <option value="">Pilih ID Ikan</option>
        <?php foreach ($tebar_bibit_options as $id_ikan => $tebar_data) : ?>
            <option value="<?php echo $id_ikan; ?>">
                <?php echo $id_ikan . ' - ' . $tebar_data['jenis_bibit'] . ' (Kolam: ' . $tebar_data['id_kolam'] . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>
    <label for="nama_pakan">Nama Pakan:</label>
    <select name="nama_pakan" id="nama_pakan" required>
        <option value="">Pilih Nama Pakan</option>
        <?php foreach ($pakan_options as $pakan) : ?>
            <option value="<?php echo $pakan; ?>"><?php echo $pakan; ?></option>
        <?php endforeach; ?>
    </select>
    <br>
    <label for="usia_pemberian_pakan">Usia Pemberian Pakan (hari):</label>
    <input type="number" name="usia_pemberian_pakan" id="usia_pemberian_pakan" required min="1">
    <br>
    <label for="pakan_harian">Pakan Harian (kg):</label>
    <input type="number" name="pakan_harian" id="pakan_harian" step="0.01" required min="0">
    <br>
    <label for="jumlah_mati">Jumlah Mati:</label>
    <input type="number" name="jumlah_mati" id="jumlah_mati" required min="0">
    <br>
    <button type="submit">Tambah</button>
    <a href="dashboard-user.php?page=pemeliharaan-ikan" class="button">Kembali</a>
</form>