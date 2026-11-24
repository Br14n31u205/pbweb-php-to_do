<?php
require 'config.php';
require_login();                       
$pdo = db();
$uid = $_SESSION['user_id'];
// BAGIAN CRUD
// Proses  (Tambah / Selesai / Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id   = (int)($_POST['id'] ?? 0);

    if ($aksi === 'tambah') {
        $teks = trim($_POST['teks'] ?? '');
        if ($teks !== '') {
            $pdo->prepare('INSERT INTO todos (user_id, teks) VALUES (?, ?)') # INSERT / CREATE
                ->execute([$uid, $teks]); 
        }
    } elseif ($aksi === 'selesai') {
        // balik atau mengubah keteranagan status:  0 -> 1 atau bisa 1 -> 0
        $pdo->prepare('UPDATE todos SET selesai = 1 - selesai WHERE id = ? AND user_id = ?') # UPDATE
            ->execute([$id, $uid]);
    } elseif ($aksi === 'hapus') {
        $pdo->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?') # DELETE
            ->execute([$id, $uid]);
    }
    header('Location: index.php');     // cegah resubmit saat refresh
    exit;
}

//  Ambil daftar to do milik user yang login 
$stmt = $pdo->prepare('SELECT * FROM todos WHERE user_id = ? ORDER BY id ASC'); # READ
$stmt->execute([$uid]);
$todos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To Do List</title>
    <link rel="stylesheet" href="styletwo.css">
</head>
<body>
<?php include 'header.php'; ?>
<main class="card">
    <div class="topbar">
        <h2>To Do List</h2>
        <span>Halo, <b><?= e($_SESSION['username']) ?></b> · <a href="logout.php">Logout</a></span>
    </div>

    <form method="post" class="form-tambah">
        <input type="hidden" name="aksi" value="tambah">
        <input type="text" name="teks" placeholder="Teks to do" required autofocus>
        <button type="submit">Tambah</button>
    </form>

    <ul class="todo">
        <?php foreach ($todos as $i => $t): ?>
            <li class="<?= $t['selesai'] ? 'done' : '' ?>" style="--i: <?= $i ?>">
                <span class="teks"><?= e($t['teks']) ?></span>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button name="aksi" value="selesai" class="btn-selesai">
                        <?= $t['selesai'] ? 'Batal' : 'Selesai' ?>
                    </button>
                    <button name="aksi" value="hapus" class="btn-hapus"
                            onclick="return confirm('Hapus item ini?')">Hapus</button>
                </form>
            </li>
        <?php endforeach; ?>
        <?php if (!$todos): ?><li class="kosong">Belum ada to do.</li><?php endif; ?>
    </ul>
</main>
</body>
</html>
