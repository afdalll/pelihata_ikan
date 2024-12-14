<?php
$user_id = $_SESSION['user_id'];

function generatePanenId($conn)
{
    $query = "SELECT id_panen FROM panen ORDER BY id_panen DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_panen'];
        $newIdNumber = (int) substr($lastId, 2) + 1;
        $newId = 'PN' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'PN001';
    }
    return $newId;
}

// Ambil data pemeliharaan ikan yang tersedia
$query_pemeliharaan = "SELECT pi.id_ikan, pi.id_kolam, pi.jenis_bibit, pi.usia_pemeliharaan, pi.tanggal_tebar
                       FROM pemeliharaan_ikan pi
                       WHERE pi.user_id = ?
                       GROUP BY pi.id_ikan";
$stmt_pemeliharaan = mysqli_prepare($conn, $query_pemeliharaan);
mysqli_stmt_bind_param($stmt_pemeliharaan, "i", $user_id);
mysqli_stmt_execute($stmt_pemeliharaan);
$result_pemeliharaan = mysqli_stmt_get_result($stmt_pemeliharaan);

$pemeliharaan_options = [];
while ($row = mysqli_fetch_assoc($result_pemeliharaan)) {
    $pemeliharaan_options[$row['id_ikan']] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ikan = filter_input(INPUT_POST, 'id_ikan', FILTER_SANITIZE_STRING);
    $tanggal_panen = filter_input(INPUT_POST, 'tanggal_panen', FILTER_SANITIZE_STRING);
    $harga_per_kg = filter_input(INPUT_POST, 'harga_per_kg', FILTER_VALIDATE_FLOAT);
    $berat_total = filter_input(INPUT_POST, 'berat_total', FILTER_VALIDATE_FLOAT);

    if ($id_ikan && $tanggal_panen && $harga_per_kg !== false && $berat_total !== false) {
        $pemeliharaan_data = $pemeliharaan_options[$id_ikan];
        $id_panen = generatePanenId($conn);
        $id_kolam = $pemeliharaan_data['id_kolam'];
        $jenis_ikan = $pemeliharaan_data['jenis_bibit'];
        $usia_pemeliharaan = $pemeliharaan_data['usia_pemeliharaan'];
        $harga_total = $harga_per_kg * $berat_total;

        // Query INSERT
        $query = "INSERT INTO panen (id_panen, user_id, id_ikan, tanggal_panen, id_kolam, jenis_ikan, usia_pemeliharaan, harga_per_kg, berat_total, harga_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Bind parameter
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssiddd", $id_panen, $user_id, $id_ikan, $tanggal_panen, $id_kolam, $jenis_ikan, $usia_pemeliharaan, $harga_per_kg, $berat_total, $harga_total);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "Data panen berhasil ditambahkan.";
            header('Location: dashboard-user.php?page=panen');
            exit();
        } else {
            $error_message = "Gagal menambahkan data panen: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Tambah Panen</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="id_ikan">ID Ikan:</label>
    <select name="id_ikan" id="id_ikan" required>
        <option value="">Pilih ID Ikan</option>
        <?php foreach ($pemeliharaan_options as $id_ikan => $pemeliharaan_data) : ?>
            <option value="<?php echo $id_ikan; ?>">
                <?php echo $id_ikan . ' - ' . $pemeliharaan_data['jenis_bibit'] . ' (Kolam: ' . $pemeliharaan_data['id_kolam'] . ')'; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>
    <label for="tanggal_panen">Tanggal Panen:</label>
    <input type="date" name="tanggal_panen" id="tanggal_panen" required>
    <br>
    <label for="harga_per_kg">Harga per/kg (Rp):</label>
    <input type="number" name="harga_per_kg" id="harga_per_kg" step="0.01" required min="0">
    <br>
    <label for="berat_total">Berat Total Ikan (kg):</label>
    <input type="number" name="berat_total" id="berat_total" step="0.01" required min="0">
    <br>
    <button type="submit">Tambah</button>
    <a href="dashboard-user.php?page=panen" class="button">Kembali</a>
</form>

<script>
document.getElementById('id_ikan').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var pemeliharaanData = <?php echo json_encode($pemeliharaan_options); ?>;
    var selectedData = pemeliharaanData[this.value];
    
    if (selectedData) {
        console.log("Usia Pemeliharaan:", selectedData.usia_pemeliharaan);
    }
});
</script>