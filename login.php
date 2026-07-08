<?php
session_start();
require_once 'koneksi.php';

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verifikasi enkripsi password
    if ($user && password_verify($password, $user['password'])) {
        // Pasang Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Arahkan sesuai jabatan
        if ($user['role'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.html");
        }
        exit();
    } else {
        $error = "Akses Ditolak! Username atau Password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Grandong Garage</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #0a0a0a; color: white; }
        .auth-box { background: #111; padding: 40px; border: 2px solid #333; border-radius: 10px; width: 100%; max-width: 400px; text-align: center; }
        .auth-box input { width: 100%; padding: 12px; margin: 10px 0; background: #1a1a1a; border: 1px solid #444; color: white; outline: none; }
        .auth-box input:focus { border-color: #ff2800; }
        .btn-auth { background: #ff2800; color: black; padding: 12px; width: 100%; border: none; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: 2px; margin-top: 15px; }
        .btn-auth:hover { background: white; box-shadow: 0 0 15px #ff2800; }
    </style>
</head>
<body>
    <div class="auth-box">
        <h2 style="color: #ff2800; margin-bottom: 20px;">GARAGE ACCESS</h2>
        <?php if($error != "") echo "<p style='color: #ff2800;'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn-auth">Login</button>
        </form>
        <p style="margin-top: 20px; font-size: 0.8rem; color: #aaa;">Belum punya akses? <a href="register.php" style="color: #ff2800;">Register di sini</a></p>
    </div>
</body>
</html>
