<?php
include 'includes/db.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard-admin.php?page=manage-users');
    exit();
}

$user_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Ambil data pengguna berdasarkan ID
$query = "SELECT username, role FROM user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($username, $role);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if ($username && $role) {
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET username = ?, password = ?, password_plain = ?, role = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssssi', $username, $password_hash, $password, $role, $user_id);
        } else {
            $query = "UPDATE user SET username = ?, role = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ssi', $username, $role, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pengguna berhasil diperbarui.";
            header('Location: dashboard-admin.php?page=manage-users');
            exit();
        } else {
            $error_message = "Gagal memperbarui pengguna: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Edit Pengguna</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
    <br>
    <label for="password">Password (kosongkan jika tidak ingin mengubah):</label>
    <input type="password" name="password" id="password">
    <br>
    <label for="role">Role:</label>
    <select name="role" id="role" required>
        <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>User</option>
        <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
    </select>
    <br>
    <button type="submit">Perbarui</button>
    <a href="dashboard-admin.php?page=manage-users" class="button">Kembali</a>
</form>