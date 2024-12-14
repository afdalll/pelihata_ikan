<?php
$id_pemeliharaan = $_GET['id'];

// Ambil data pemeliharaan ikan yang akan diedit
$query = "SELECT * FROM pemeliharaan_ikan WHERE id_pemeliharaan = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "si", $id_pemeliharaan, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: dashboard.php?page=pemeliharaan-ikan');
    exit();
}

$pemeliharaan_ikan = mysqli_fetch_assoc($result);

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

function updatePanenData($conn, $id_ikan, $user_id, $usia_pemeliharaan) {
    $query = "UPDATE panen 
              SET usia_pemeliharaan = ? 
              WHERE id_ikan = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isi", $usia_pemeliharaan, $id_ikan, $user_id);
    return mysqli_stmt_execute($stmt);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pakan = filter_input(INPUT_POST, 'nama_pakan', FILTER_SANITIZE_STRING);
    $usia_pemberian_pakan = filter_input(INPUT_POST, 'usia_pemberian_pakan', FILTER_VALIDATE_INT);
    $pakan_harian = filter_input(INPUT_POST, 'pakan_harian', FILTER_VALIDATE_FLOAT);
    $jumlah_mati = filter_input(INPUT_POST, 'jumlah_mati', FILTER_VALIDATE_INT);

    if ($nama_pakan && $usia_pemberian_pakan !== false && $pakan_harian !== false && $jumlah_mati !== false) {
        // Hitung jumlah hidup
        $jumlah_hidup = $pemeliharaan_ikan['jumlah_awal'] - $jumlah_mati;

        // Hitung usia pemeliharaan
        $tanggal_sekarang = new DateTime();
        $tanggal_tebar_obj = new DateTime($pemeliharaan_ikan['tanggal_tebar']);
        $usia_pemeliharaan = $tanggal_sekarang->diff($tanggal_tebar_obj)->days;

        // Update data pemeliharaan saat ini
        $query = "UPDATE pemeliharaan_ikan SET nama_pakan = ?, usia_pemberian_pakan_akhir = ?, pakan_harian = ?, jumlah_mati = ?, jumlah_hidup = ?, usia_pemeliharaan = ? WHERE id_pemeliharaan = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sidiiisi", $nama_pakan, $usia_pemberian_pakan, $pakan_harian, $jumlah_mati, $jumlah_hidup, $usia_pemeliharaan, $id_pemeliharaan, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            // Update data berikutnya
            $query_next = "SELECT id_pemeliharaan, usia_pemberian_pakan_awal, usia_pemberian_pakan_akhir, jumlah_awal, jumlah_mati 
                           FROM pemeliharaan_ikan 
                           WHERE id_ikan = ? AND user_id = ? AND usia_pemberian_pakan_awal > ?
                           ORDER BY usia_pemberian_pakan_awal ASC";
            $stmt_next = mysqli_prepare($conn, $query_next);
            mysqli_stmt_bind_param($stmt_next, "sii", $pemeliharaan_ikan['id_ikan'], $user_id, $pemeliharaan_ikan['usia_pemberian_pakan_awal']);
            mysqli_stmt_execute($stmt_next);
            $result_next = mysqli_stmt_get_result($stmt_next);

            $prev_jumlah_hidup = $jumlah_hidup;
            $prev_usia_akhir = $usia_pemberian_pakan;

            while ($row_next = mysqli_fetch_assoc($result_next)) {
                $new_usia_awal = $prev_usia_akhir + 1;
                $new_jumlah_awal = $prev_jumlah_hidup;
                $new_jumlah_hidup = $new_jumlah_awal - $row_next['jumlah_mati'];

                $query_update = "UPDATE pemeliharaan_ikan 
                                 SET usia_pemberian_pakan_awal = ?, 
                                     jumlah_awal = ?, 
                                     jumlah_hidup = ? 
                                 WHERE id_pemeliharaan = ?";
                $stmt_update = mysqli_prepare($conn, $query_update);
                mysqli_stmt_bind_param($stmt_update, "iiis", $new_usia_awal, $new_jumlah_awal, $new_jumlah_hidup, $row_next['id_pemeliharaan']);
                
                if (!mysqli_stmt_execute($stmt_update)) {
                    error_log("Failed to update next record: " . mysqli_error($conn));
                }

                $prev_jumlah_hidup = $new_jumlah_hidup;
                $prev_usia_akhir = $row_next['usia_pemberian_pakan_akhir'];
            }

            // Update data panen
            if (!updatePanenData($conn, $pemeliharaan_ikan['id_ikan'], $user_id, $usia_pemeliharaan)) {
                error_log("Failed to update panen data: " . mysqli_error($conn));
            }

            $_SESSION['success_message'] = "Data pemeliharaan ikan berhasil diperbarui.";
            header('Location: dashboard-user.php?page=pemeliharaan-ikan');
            exit();
        } else {
            $error_message = "Gagal memperbarui data pemeliharaan ikan: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Pemeliharaan Ikan</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <p>ID Pemeliharaan: <?php echo $id_pemeliharaan; ?></p>
    <label for="nama_pakan">Nama Pakan:</label>
    <select name="nama_pakan" id="nama_pakan" required>
        <?php foreach ($pakan_options as $pakan) : ?>
            <option value="<?php echo $pakan; ?>" <?php echo ($pakan == $pemeliharaan_ikan['nama_pakan']) ? 'selected' : ''; ?>>
                <?php echo $pakan; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>
    <label for="usia_pemberian_pakan">Usia Pemberian Pakan (hari):</label>
    <input type="number" name="usia_pemberian_pakan" id="usia_pemberian_pakan" required min="<?php echo $pemeliharaan_ikan['usia_pemberian_pakan_awal']; ?>" value="<?php echo htmlspecialchars($pemeliharaan_ikan['usia_pemberian_pakan_akhir']); ?>">
    <br>
    <label for="pakan_harian">Pakan Harian (kg):</label>
    <input type="number" name="pakan_harian" id="pakan_harian" step="0.01" required min="0" value="<?php echo htmlspecialchars($pemeliharaan_ikan['pakan_harian']); ?>">
    <br>
    <label for="jumlah_mati">Jumlah Mati:</label>
    <input type="number" name="jumlah_mati" id="jumlah_mati" required min="0" value="<?php echo htmlspecialchars($pemeliharaan_ikan['jumlah_mati']); ?>">
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-user.php?page=pemeliharaan-ikan" class="button">Kembali</a>
</form>