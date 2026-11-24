<?php
require 'config.php';

// Kalau sudah login, tidak perlu ke halaman login lagi
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // password_verify membandingkan input dengan hash di database
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);          // cegah session fixation
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - To Do List</title>
    <link rel="stylesheet" href="styletwo.css">
</head>
<body>
<?php include 'header.php'; ?>
<main class="card kecil">
    <h2>Login</h2>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" class="form-login">
        <label>User Name
            <input type="text" name="username" required autofocus>
        </label>
        <label>Password
            <input type="password" name="password" required>
            <button type="button" class="mata" onclick="lihat(this)">Lihat</button>
        </label>
        <button type="submit">Submit</button>
    </form>
    <p class="link">Belum punya akun? <a href="register.php">Daftar</a></p>
</main>
    <script>
        function lihat(btn) {
            const input = btn.previousElementSibling;
            const tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            btn.textContent = tampil ? 'Sembunyi' : 'Lihat';
        }
    </script>
</body>
</html>
