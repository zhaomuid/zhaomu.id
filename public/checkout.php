<?php
require_once '../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    header("Location: index.php");
    exit;
}

$pesan_sukses = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pemesan = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $jumlah = intval($_POST['jumlah']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nama_produk = $produk['nama_produk'];

    if ($jumlah > 0 && $jumlah <= $produk['stok']) {
        // Kurangi stok di database
        $stok_baru = $produk['stok'] - $jumlah;
        mysqli_query($conn, "UPDATE produk SET stok = $stok_baru WHERE id = $id");
        
        // Simpan ke tabel pesanan beserta no_hp
        mysqli_query($conn, "INSERT INTO pesanan (nama_pemesan, no_hp, nama_produk, jumlah, alamat) VALUES ('$nama_pemesan', '$no_hp', '$nama_produk', $jumlah, '$alamat')");
        
        $pesan_sukses = "Terima kasih, $nama_pemesan! Pesanan untuk <b>{$nama_produk}</b> ($jumlah pcs) berhasil dibuat & tercatat. Kami akan segera menghubungi nomor WhatsApp Anda. ✨";
        $produk['stok'] = $stok_baru;
    } else {
        $error = "Jumlah pesanan tidak valid atau melebihi stok yang tersedia!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Zhaomu.id</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fff0f5; color: #4a4a4a; padding: 40px; }
        .box { max-width: 500px; background: white; margin: 0 auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { color: #d87093; margin-top: 0; }
        label { display: block; margin-top: 15px; font-weight: bold; font-size: 14px; }
        input, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #d87093; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; margin-top: 20px; font-weight: bold; cursor: pointer; font-size: 16px; }
        button:hover { background: #c71585; }
        .back { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 14px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="box">
    <h2>🌸 Konfirmasi Pesanan</h2>
    
    <?php if(!empty($pesan_sukses)): ?>
        <div class="alert-success"><?= $pesan_sukses; ?></div>
    <?php endif; ?>

    <?php if(!empty($error)): ?>
        <div class="alert-error"><?= $error; ?></div>
    <?php endif; ?>

    <h3><?= $produk['nama_produk']; ?></h3>
    <p>Stok Tersedia: <b><?= $produk['stok']; ?> pcs</b></p>
    <p style="font-size: 14px; color: #666;"><?= $produk['deskripsi']; ?></p>

    <form method="POST">
        <label>Nama Pemesan:</label>
        <input type="text" name="nama" required placeholder="Masukkan nama kamu">

        <label>Nomor HP / WhatsApp:</label>
        <input type="text" name="no_hp" required placeholder="Contoh: 081234567890">

        <label>Jumlah Beli:</label>
        <input type="number" name="jumlah" value="1" min="1" max="<?= $produk['stok']; ?>" required>

        <label>Alamat Pengiriman:</label>
        <textarea name="alamat" rows="3" required placeholder="Masukkan alamat lengkap..."></textarea>

        <button type="submit">Proses Pesanan Sekarang</button>
    </form>

    <a href="index.php" class="back">← Kembali ke Katalog Produk</a>
</div>

</body>
</html>