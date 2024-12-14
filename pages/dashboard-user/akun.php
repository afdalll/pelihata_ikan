<?php
// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Ambil data user
$query = "SELECT username, password_plain FROM user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_username'])) {
        $new_username = trim($_POST['new_username']);
        
        if (!empty($new_username)) {
            $update_query = "UPDATE user SET username = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $new_username, $user_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['username'] = $new_username;
                $success_message = "Username berhasil diperbarui.";
            } else {
                $error_message = "Gagal memperbarui username.";
            }
        } else {
            $error_message = "Username tidak boleh kosong.";
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            // Verifikasi password saat ini
            if ($current_password === $user['password_plain']) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE user SET password = ?, password_plain = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ssi", $hashed_password, $new_password, $user_id);
                
                if ($update_stmt->execute()) {
                    $success_message = "Password berhasil diperbarui.";
                } else {
                    $error_message = "Gagal memperbarui password.";
                }
            } else {
                $error_message = "Password saat ini tidak sesuai.";
            }
        } else {
            $error_message = "Password baru dan konfirmasi password tidak cocok.";
        }
    }
}
?>

<h2>Pengaturan Akun</h2>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="account-settings">
    <form action="" method="post" class="update-form">
        <h3>Ubah Username</h3>
        <div class="form-group">
            <label for="new_username">Username Baru:</label>
            <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <button type="submit" name="update_username" class="btn btn-primary">Perbarui Username</button>
    </form>

    <form action="" method="post" class="update-form">
        <h3>Ubah Password</h3>
        <div class="form-group">
            <label for="current_password">Password Saat Ini:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">Password Baru:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password Baru:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" name="update_password" class="btn btn-primary">Perbarui Password</button>
    </form>
</div>