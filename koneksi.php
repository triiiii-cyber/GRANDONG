<?php
$host     = 'localhost';
$port     = '3307';        // Port MySQL kamu
$db_name  = 'dbgrandong';
$username = 'root';        // Sesuaikan dengan user MySQL kamu
$password = '';            // Sesuaikan — biasanya kosong di XAMPP/Laragon lokal

// DSN = Data Source Name, format koneksi PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    
    // Set error mode: kalau ada error, lempar exception (biar gampang debug)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set hasil fetch sebagai associative array (biar bisa akses $row['nama_part'])
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Kalau koneksi gagal, hentikan eksekusi dan tampilkan error sebagai JSON
    http_response_code(500);
    echo json_encode(['error' => 'Koneksi database gagal: ' . $e->getMessage()]);
    die();
}
?>
