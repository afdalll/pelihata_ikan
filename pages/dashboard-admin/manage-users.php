<?php
include 'includes/db.php';


// Tangani penghapusan pengguna
if (isset($_GET['delete'])) {
    $user_id = filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_NUMBER_INT);

    // Ambil username dari user_id yang akan dihapus
    $sql = "SELECT username FROM user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($username_to_delete);
    $stmt->fetch();
    $stmt->close();

    // Periksa apakah username yang akan dihapus adalah username yang sedang login
    if ($username_to_delete === $_SESSION['username']) {
        echo "<script>alert('Maaf, username sedang digunakan!'); window.location.href='dashboard-admin.php?page=manage-users';</script>";
    } else {
        // Panggil fungsi deleteData untuk menghapus user dan data terkait
        $isAdmin = ($_SESSION['role'] === 'admin');
        $result = deleteData($conn, 'user', $user_id, $_SESSION['user_id'], $isAdmin);
        if ($result) {
            header('Location: dashboard-admin.php?page=manage-users');
        } else {
            echo "<script>alert('Gagal menghapus pengguna!'); window.location.href='dashboard-admin.php?page=manage-users';</script>";
        }
        exit();
    }
}

// Ambil semua pengguna dari database
$sql = "SELECT id, username, password_plain, role, created_at FROM user";
$result = $conn->query($sql);
?>

<h2>Manage Users</h2>
<div class="data-container">
    <div class="data-header">
        <input type="text" id="search-input" placeholder="Cari pengguna...">
        <button id="search-button">Cari</button>
        <button id="add-data-button" onclick="window.location.href='dashboard-admin.php?page=input-user'">Tambah Pengguna</button>
    </div>
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['password_plain']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo '<td>
                            <form method="GET" action="dashboard-admin.php" style="display:inline;">
                                <input type="hidden" name="page" value="edit-user">
                                <input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">
                                <button type="submit" class="edit-button">Edit</button>
                            </form>
                            <button type="button" class="delete-button" data-id="' . htmlspecialchars($row['id']) . '" data-table="user" data-return-page="manage-users">Delete</button>
                        </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada pengguna</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>