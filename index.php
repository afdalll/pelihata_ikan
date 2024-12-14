<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed_pages = ['home', 'about', 'kontak', 'login', 'registrasi'];

$page_titles = [
    'home' => 'Home - Website Ikan',
    'about' => 'About - Website Ikan',
    'kontak' => 'Kontak - Website Ikan',
    'login' => 'Login - Website Ikan',
    'registrasi' => 'Registrasi - Website Ikan',
];

$title = isset($page_titles[$page]) ? $page_titles[$page] : 'Website Ikan';

$background_image = 'background-image.png'; // Ganti dengan nama file gambar latar belakang Anda

$body_style = 'style="background-image: url(\'assets/images/' . $background_image . '\');"';

include 'includes/header.php';

if (in_array($page, $allowed_pages)) {
    if (in_array($page, ['login', 'registrasi'])) {
        include "pages/auth/$page.php";
    } else {
        include "pages/$page.php";
    }
} else {
    include 'pages/home.php';
}

include 'includes/footer.php';
