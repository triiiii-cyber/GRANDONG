<?php
// --- TAMBAHKAN BLOK GEMBOK INI ---
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// 1. KONEKSI DATABASE PDO
$host = "localhost";
$username = "root";
$password = "";
$dbname = "dbgrandong"; // sesuaikan nama db kamu
$port = 3307; // port MySQL xampp kamu

try {
    $db = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi Mesin Gagal: " . $e->getMessage());
}

// 2. PROSES INTERAKSI CRUD OPERATION (BACKEND LOGIC)

// A. Tambah Data (CREATE) & Edit Data (UPDATE ACTION)
if (isset($_POST['simpan_part'])) {
    $id = $_POST['id'];
    $nama_part = $_POST['nama_part'];
    $brand = $_POST['brand'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar_lama']; 

    if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar_file']['tmp_name'];
        $file_name = $_FILES['gambar_file']['name'];
        
        // Ekstrak format file (jpg, png, dll)
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Buat nama unik agar file tidak saling tertimpa jika namanya sama
        $nama_file_baru = uniqid('part_', true) . '.' . $file_ext;
        
        // Arahkan ke folder assets milikmu
        $path_tujuan = 'assets/' . $nama_file_baru;
        
        // Pindahkan file dari memori sementara (temp) ke folder assets
        if (move_uploaded_file($file_tmp, $path_tujuan)) {
            $gambar = $path_tujuan; // Timpa $gambar dengan path yang baru
        }
    }
    // --- AKHIR LOGIKA UPLOAD GAMBAR ---
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];
    $tipe = $_POST['tipe'];
    $deskripsi = $_POST['deskripsi'];
    $spesifikasi = $_POST['spesifikasi']; // disarankan kirim format JSON kaku, misal: {"Lebar":"9.5"}

    if (empty($id)) {
        // Mode Insert Baru
        $sql = "INSERT INTO parts (nama_part, brand, harga, gambar, stok, kategori, tipe, deskripsi, spesifikasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nama_part, $brand, $harga, $gambar, $stok, $kategori, $tipe, $deskripsi, $spesifikasi]);
    } else {
        // Mode Update Edit Data Lama
        $sql = "UPDATE parts SET nama_part=?, brand=?, harga=?, gambar=?, stok=?, kategori=?, tipe=?, deskripsi=?, spesifikasi=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nama_part, $brand, $harga, $gambar, $stok, $kategori, $tipe, $deskripsi, $spesifikasi, $id]);
    }
    header("Location: admin.php");
    exit();
}

// B. Proses Hapus Data (DELETE)
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $sql = "DELETE FROM parts WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_hapus]);
    header("Location: admin.php");
    exit();
}

// C. Ambil Data Spesifik untuk Tombol Edit (READ SINGLE)
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $sql = "SELECT * FROM parts WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_edit]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// D. Tarik Semua Data Inventaris untuk Ditampilkan di Tabel (READ ALL)
$query = $db->query("SELECT * FROM parts ORDER BY id DESC");
$all_parts = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grandong - Garage Control Panel</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Segoe UI', sans-serif; margin: 30px; }
        h1, h2 { color: #ff2800; letter-spacing: 2px; text-transform: uppercase; border-bottom: 2px solid #ff2800; padding-bottom: 10px; }
        .form-container { background-color: #111; padding: 25px; border: 2px solid #222; margin-bottom: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        label { font-weight: bold; color: #aaa; font-size: 13px; text-transform: uppercase; }
        input, select, textarea { width: 100%; padding: 10px; background-color: #1a1a1a; border: 1px solid #333; color: #fff; margin-top: 5px; box-sizing: border-box; }
        input:focus, select:focus, textarea:focus { border-color: #ff2800; outline: none; }
        .btn-submit { background-color: #ff2800; color: #000; font-weight: bold; border: none; padding: 12px; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; width: 100%; margin-top: 20px; transition: 0.3s; }
        .btn-submit:hover { background-color: #fff; color: #000; box-shadow: 0 0 15px #ff2800; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #111; border: 1px solid #222; }
        th, td { padding: 12px; text-align: left; border: 1px solid #222; font-size: 14px; }
        th { background-color: #1a1a1a; color: #ff2800; text-transform: uppercase; }
        tr:hover { background-color: #161616; }
        .btn-action { padding: 5px 12px; font-size: 12px; font-weight: bold; text-decoration: none; text-transform: uppercase; display: inline-block; margin-right: 5px; }
        .btn-edit { background-color: #007bff; color: #fff; }
        .btn-hapus { background-color: #dc3545; color: #fff; }
        .btn-batal { background-color: #555; color: #fff; text-decoration: none; padding: 10px 15px; display: inline-block; margin-top: 10px; text-transform: uppercase; font-size: 12px; font-weight: bold; }
        img { width: 50px; height: 50px; object-fit: cover; border: 1px solid #333; }
    </style>
</head>
<body>

    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff2800; padding-bottom: 10px; margin-bottom: 20px;">
    <h1 style="border: none; padding: 0; margin: 0;">GRANDONG - GARAGE CONTROL PANEL</h1>
    
    <div style="display: flex; align-items: center; gap: 15px;">
        <span style="color: #aaa; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
            Mechanic on Duty: <span style="color: #ff2800;"><?= htmlspecialchars($_SESSION['username'] ?? 'Diazz') ?></span>
        </span>
        <a href="logout.php" style="background-color: #dc3545; color: white; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 5px; text-transform: uppercase;">Logout</a>
    </div>
</div>
    <div class="form-container">
        <h2><?= $edit_data ? 'EDIT PERFORMANCE PART' : 'ADD NEW PERFORMANCE PART' ?></h2>
        <form action="admin.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
            
            <div class="form-grid">
                <div>
                    <label>Nama Sparepart</label>
                    <input type="text" name="nama_part" required value="<?= $edit_data['nama_part'] ?? '' ?>" placeholder="Example: Enkei RPF1 Forged">
                </div>
                <div>
                    <label>Brand</label>
                    <input type="text" name="brand" required value="<?= $edit_data['brand'] ?? '' ?>" placeholder="Example: Enkei / Brembo">
                </div>
                <div>
                    <label>Harga (Rupiah)</label>
                    <input type="number" name="harga" required value="<?= $edit_data['harga'] ?? '' ?>" placeholder="Example: 18500000">
                </div>
                <div>
                    <label>Upload Gambar Part</label>
                    <input type="file" name="gambar_file" accept="image/*" style="padding: 7px;">
                    
                    <input type="hidden" name="gambar_lama" value="<?= $edit_data['gambar'] ?? '' ?>">
                    
                    <?php if (!empty($edit_data['gambar'])): ?>
                        <br><small style="color: #aaa;">Saat ini: <?= htmlspecialchars($edit_data['gambar']) ?></small>
                    <?php endif; ?>
                </div>
                <div>
                    <label>Stok Barang</label>
                    <input type="number" name="stok" required value="<?= $edit_data['stok'] ?? '10' ?>">
                </div>
                <div>
                    <label>Kategori</label>
                    <select name="kategori">
                        <option value="Velg" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Velg') ? 'selected' : '' ?>>Velg</option>
                        <option value="Ban" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Ban') ? 'selected' : '' ?>>Ban</option>
                        <option value="Pengereman" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Pengereman') ? 'selected' : '' ?>>Pengereman</option>
                        <option value="Suspensi" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Suspensi') ? 'selected' : '' ?>>Suspensi</option>
                        <option value="Knalpot" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Knalpot') ? 'selected' : '' ?>>Knalpot</option>
                        <option value="Mesin" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Mesin') ? 'selected' : '' ?>>Mesin</option>
                        <option value="Body Kit" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Body Kit') ? 'selected' : '' ?>>Body Kit</option>
                        <option value="Interior" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Interior') ? 'selected' : '' ?>>Interior</option>
                        <option value="Elektronik" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Elektronik') ? 'selected' : '' ?>>Elektronik</option>
                        <option value="Aksesoris" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Aksesoris') ? 'selected' : '' ?>>Aksesoris</option>
                        <option value="Lainnya" <?= (isset($edit_data['kategori']) && $edit_data['kategori'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label>Tipe Skenario</label>
                    <input type="text" name="tipe" value="<?= $edit_data['tipe'] ?? '' ?>" placeholder="Example: 'Track Day', 'Daily Use', 'Off-Road'">
                </div>
                <div>
                    <label>Spesifikasi (Format JSON)</label>
                    <input type="text" name="spesifikasi" value='<?= $edit_data['spesifikasi'] ?? '' ?>' placeholder= "Example: {'Lebar':'9.5','Diameter':'18'}">
                </div>
            </div>

            <div style="margin-top: 15px;">
                <label>Deskripsi Produk</label>
                <textarea name="deskripsi" rows="3" required placeholder="Type product description here..."><?= $edit_data['deskripsi'] ?? '' ?></textarea>
            </div>

            <button type="submit" name="simpan_part" class="btn-submit">
                <?= $edit_data ? 'UPDATE DATA' : 'INJECT TO DATABASE' ?>
            </button>
            <?php if ($edit_data): ?>
                <a href="admin.php" class="btn-batal">Batal Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <h2>INVENTARIS GARAGE CURRENTLY IN DATABASE (<?= count($all_parts) ?> PARTS)</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Visual</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_parts as $part): ?>
            <tr>
                <td><?= $part['id'] ?></td>
                <td>
                    <?php if (!empty($part['gambar'])): ?>
                        <img src="<?= $part['gambar'] ?>" alt="part img" onerror="this.src='https://placehold.co/50x50/111/ff2800?text=Broken'">
                    <?php else: ?>
                        <img src="https://placehold.co/50x50/111/ff2800?text=No+Img" alt="no img">
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($part['nama_part']) ?></strong><br><small style="color:#666">Brand: <?= htmlspecialchars($part['brand']) ?></small></td>
                <td><?= htmlspecialchars($part['kategori']) ?> (<?= htmlspecialchars($part['tipe']) ?>)</td>
                <td style="color:#ff2800; font-weight:bold;">Rp <?= number_format($part['harga'], 0, ',', '.') ?></td>
                <td><?= $part['stok'] ?></td>
                <td>
                    <a href="admin.php?edit=<?= $part['id'] ?>" class="btn-action btn-edit">Edit</a>
                    <a href="admin.php?hapus=<?= $part['id'] ?>" onclick="return confirm('Yakin ingin menghapus komponen gahar ini?');" class="btn-action btn-hapus">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
