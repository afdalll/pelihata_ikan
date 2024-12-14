<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if ($username && $password && $role) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO user (username, password, password_plain, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssss', $username, $password_hash, $password, $role);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pengguna berhasil ditambahkan.";
            header('Location: dashboard-admin.php?page=manage-users');
            exit();
        } else {
            $error_message = "Gagal menambahkan pengguna: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Mohon isi semua field dengan benar.";
    }
}
?>

<h2>Tambah Pengguna</h2>
<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="" class="edit-input-form">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <label for="role">Role:</label>
    <select name="role" id="role" required>
        <option value="">Pilih Role</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>
    <br>
    <button type="submit">Tambah</button>
    <a href="dashboard-admin.php?page=manage-users" class="button">Kembali</a>
</form>