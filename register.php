<?php
session_start();
require_once 'koneksi.php';

$pesan = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // PERBAIKAN KEAMANAN: Role dipaksa menjadi 'user' secara otomatis.
    $role = 'user'; 

    $stmt_cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt_cek->execute([$username]);
    
    if ($stmt_cek->rowCount() > 0) {
        $pesan = "Pendaftaran Gagal: Username sudah digunakan mekanik lain!";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $password_hashed, $role])) {
            $pesan = "Akun berhasil ditempa! Silakan Login.";
        } else {
            $pesan = "Gagal membuat akun.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Grandong Garage</title>
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
        <h2 style="color: #ff2800; margin-bottom: 20px;">JOIN THE GARAGE</h2>
        <?php if($pesan != "") echo "<p style='color: #27ae60;'>$pesan</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register" class="btn-auth">Register</button>
        </form>
        <p style="margin-top: 20px; font-size: 0.8rem; color: #aaa;">Sudah punya akses? <a href="login.php" style="color: #ff2800;">Login di sini</a></p>
    </div>
</body>
</html>
