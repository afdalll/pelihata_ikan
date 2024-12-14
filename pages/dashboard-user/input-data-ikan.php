<?php

$user_id = $_SESSION['user_id'];

function generateIkanId($conn)
{
    $query = "SELECT id_ikan FROM data_ikan ORDER BY id_ikan DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['id_ikan'];
        $newIdNumber = (int) substr($lastId, 3) + 1;
        $newId = 'IKN' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    } else {
        $newId = 'IKN001';
    }
    return $newId;
}

// Ambil data kolam yang tersedia
$query_kolam = "SELECT id_kolam, kapasitas_ikan FROM data_kolam WHERE user_id = ? AND id_kolam NOT IN (SELECT id_kolam FROM data_ikan WHERE user_id = ?)";
$stmt_kolam = mysqli_prepare($conn, $query_kolam);
mysqli_stmt_bind_param($stmt_kolam, "ii", $user_id, $user_id);
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
            $id_ikan = generateIkanId($conn);
            $query = "INSERT INTO data_ikan (id_ikan, user_id, jenis_bibit, id_kolam, jumlah_bibit, tanggal_masuk_bibit) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssss", $id_ikan, $user_id, $jenis_bibit, $id_kolam, $jumlah_bibit, $tanggal_masuk_bibit);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Data ikan berhasil ditambahkan.";
                header('Location: dashboard-user.php?page=data-ikan');
                exit();
            } else {
                $error_message = "Gagal menambahkan data ikan: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Jumlah bibit melebihi maksimum kolam (" . $kolam_options[$id_kolam] . ")!";
        }
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Tambah Data Ikan</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="jenis_bibit">Jenis Bibit:</label>
    <select name="jenis_bibit" id="jenis_bibit" required>
        <option value="">Pilih Jenis Bibit</option>
        <option value="Ikan Nila">Ikan Nila</option>
        <option value="Ikan Lele">Ikan Lele</option>
        <option value="Ikan Mas">Ikan Mas</option>
    </select>
    <br>
    <label for="id_kolam">ID Kolam:</label>
    <select name="id_kolam" id="id_kolam" required onchange="updateMaxJumlahBibit()">
        <option value="">Pilih ID Kolam</option>
        <?php foreach ($kolam_options as $id_kolam => $kapasitas) : ?>
            <option value="<?php echo $id_kolam; ?>" data-kapasitas="<?php echo $kapasitas; ?>"><?php echo $id_kolam; ?></option>
        <?php endforeach; ?>
    </select>
    <br>
    <label for="jumlah_bibit">Jumlah Bibit:</label>
    <input type="number" name="jumlah_bibit" id="jumlah_bibit" required min="1">
    <br>
    <label for="tanggal_masuk_bibit">Tanggal Masuk Bibit:</label>
    <input type="date" name="tanggal_masuk_bibit" id="tanggal_masuk_bibit" required>
    <br>
    <button type="submit">Tambah</button>
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