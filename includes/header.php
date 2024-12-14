<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body <?php echo $body_style; ?>>
    <header>
        <img src="assets/images/logo.png" alt="logo.png" class="logo">
        <h1>Website Ikan</h1>
        <nav>
            <ul>
            <li><a href="index.php?page=home">Home</a></li>
                <li><a href="index.php?page=login" class="login-button">Login</a></li>
            </ul>
        </nav>
    </header>
</body>
</html>