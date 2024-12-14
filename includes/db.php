<?php
$host = 'localhost';
$db = 'wb_ikan';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Koneksi ke database gagal: ' . $conn->connect_error);
}
