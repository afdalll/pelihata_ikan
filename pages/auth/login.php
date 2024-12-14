<?php
include 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, role FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $password_hash, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $password_hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username; // Tambahkan baris ini untuk menyimpan username ke dalam sesi
        $_SESSION['role'] = $role;

        if ($role === 'admin') {
            header('Location: dashboard-admin.php');
        } else {
            header('Location: dashboard-user.php');
        }
        exit();
    } else {
        echo "Username atau password salah!";
    }

    $stmt->close();
}
?>

<div class="login-registration-page">
    <h1>Login</h1>
    <form action="" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="index.php?page=registrasi">Daftar di sini</a></p>
</div>
