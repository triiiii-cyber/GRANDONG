<?php
// Izinkan frontend (JS) mengakses file ini dari domain yang sama
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Panggil koneksi — $pdo langsung tersedia setelah ini
require_once 'koneksi.php';

try {
    // Pakai alias AS untuk menyamakan nama kolom DB dengan properti di script.js
    // nama_part → nama (biar script.js tidak perlu diubah)
    $stmt = $pdo->query("
        SELECT 
            id,
            nama_part   AS nama,
            brand,
            harga,
            gambar,
            stok,
            kategori,
            tipe,
            deskripsi,
            spesifikasi
        FROM parts
    ");

    $data = $stmt->fetchAll();

    // Kolom spesifikasi di DB bertipe JSON (string),
    // perlu di-decode dulu biar JS bisa baca sebagai object
    foreach ($data as &$row) {
        $row['id'] = (int)$row['id']; // Pastikan id dikirim sebagai integer
        $row['spesifikasi'] = json_decode($row['spesifikasi']);
    }

    echo json_encode($data);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal ambil data: ' . $e->getMessage()]);
}
?>
