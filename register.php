<?php
// untuk membuat user baru (selain admin ya!)
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$sukses = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $error = 'Username 3-50 karakter: huruf, angka, atau underscore.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $cek = db()->prepare('SELECT id FROM users WHERE username = ?');
        $cek->execute([$username]);
        if ($cek->fetch()) {
            $error = 'Username sudah dipakai.';
        } else {
            // password disimpan sebagai hash, sama seperti admin
            db()->prepare('INSERT INTO users (username, password) VALUES (?, ?)')
                ->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
            $sukses = 'Akun berhasil dibuat. Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - To Do List</title>
    <link rel="stylesheet" href="styletwo.css">
</head>
<body>
<?php include 'header.php'; ?>
<main class="card kecil">
    <h2>Daftar</h2>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <?php if ($sukses): ?><p class="sukses"><?= e($sukses) ?></p><?php endif; ?>
    <form method="post" class="form-login">
        <label>User Name
            <input type="text" name="username" required autofocus>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <label>Ulangi Password
            <input type="password" name="konfirmasi" required>
        </label>
        <button type="submit">Daftar</button>
    </form>
    <p class="link">Sudah punya akun? <a href="login.php">Login</a></p>
</main>
</body>
</html>
