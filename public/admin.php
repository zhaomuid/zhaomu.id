<?php
require_once '../config/database.php';

$pesan = "";

// Proses Tambah Produk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok = intval($_POST['stok']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $foto = mysqli_real_escape_string($conn, $_POST['foto']);

    $query = "INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi, foto) VALUES ('$nama', '$kategori', 0, $stok, '$deskripsi', '$foto')";
    if (mysqli_query($conn, $query)) {
        $pesan = "Produk baru berhasil ditambahkan! ✨";
    }
}

// Proses Edit Produk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok = intval($_POST['stok']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $foto = mysqli_real_escape_string($conn, $_POST['foto']);

    $query = "UPDATE produk SET nama_produk='$nama', kategori='$kategori', stok=$stok, deskripsi='$deskripsi', foto='$foto' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        $pesan = "Produk berhasil diperbarui! ✨";
    }
}

// Proses Hapus Produk
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM produk WHERE id = $id_hapus");
    header("Location: admin.php");
    exit;
}

$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id_edit = intval($_GET['edit_id']);
    $res_edit = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id_edit");
    $edit_data = mysqli_fetch_assoc($res_edit);
}

$result_produk = mysqli_query($conn, "SELECT * FROM produk");
$result_pesanan = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Zhaomu.id</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fff0f5; color: #4a4a4a; padding: 30px; }
        .container { max-width: 1000px; background: white; margin: 0 auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2, h3 { color: #d87093; }
        form { background: #faf0f2; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        label { display: block; margin-top: 10px; font-weight: bold; font-size: 13px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #d87093; color: white; border: none; padding: 10px 20px; border-radius: 6px; margin-top: 15px; font-weight: bold; cursor: pointer; }
        button:hover { background: #c71585; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; margin-bottom: 40px; }
        th, td { border: 1px solid #eee; padding: 10px; text-align: left; }
        th { background: #ffe4e1; color: #d87093; }
        .btn-del { background: #ff6b6b; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; }
        .btn-del:hover { background: #fa5252; }
        .btn-edit { background: #4dabf7; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-right: 5px; }
        .btn-edit:hover { background: #339af0; }
        .back { display: inline-block; margin-bottom: 20px; color: #d87093; text-decoration: none; font-weight: bold; }
        .alert { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back">← Lihat Website / Katalog</a>
    <h2>⚙️ Panel Admin Zhaomu.id</h2>

    <?php if($pesan): ?>
        <div class="alert"><?= $pesan; ?></div>
    <?php endif; ?>

    <?php if($edit_data): ?>
        <form method="POST">
            <h3>Edit Produk</h3>
            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
            
            <label>Nama Produk:</label>
            <input type="text" name="nama" value="<?= $edit_data['nama_produk']; ?>" required>

            <label>Kategori:</label>
            <input type="text" name="kategori" value="<?= $edit_data['kategori']; ?>" required>

            <label>Stok:</label>
            <input type="number" name="stok" value="<?= $edit_data['stok']; ?>" required>

            <label>Nama File Foto (di folder img/):</label>
            <input type="text" name="foto" value="<?= $edit_data['foto']; ?>" placeholder="contoh: pearl.png">

            <label>Deskripsi:</label>
            <textarea name="deskripsi" rows="2"><?= $edit_data['deskripsi']; ?></textarea>

            <button type="submit" name="edit">Simpan Perubahan</button>
            <a href="admin.php" style="margin-left: 10px; color: #888; text-decoration: none;">Batal</a>
        </form>
    <?php else: ?>
        <form method="POST">
            <h3>Tambah Produk Baru</h3>
            <label>Nama Produk:</label>
            <input type="text" name="nama" required>

            <label>Kategori:</label>
            <input type="text" name="kategori" placeholder="contoh: Bracelet Collection" required>

            <label>Stok:</label>
            <input type="number" name="stok" required>

            <label>Nama File Foto (di folder img/):</label>
            <input type="text" name="foto" placeholder="contoh: tulip.png" required>

            <label>Deskripsi:</label>
            <textarea name="deskripsi" rows="2"></textarea>

            <button type="submit" name="tambah">Simpan Produk</button>
        </form>
    <?php endif; ?>

    <!-- Daftar Pesanan Masuk -->
    <h3>📦 Daftar Pesanan Masuk dari Pelanggan</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Nama Pemesan</th>
            <th>No. HP / WA</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Alamat</th>
            <th>Waktu</th>
        </tr>
        <?php $no_p = 1; while($p = mysqli_fetch_assoc($result_pesanan)) : ?>
        <tr>
            <td><?= $no_p++; ?></td>
            <td><b><?= $p['nama_pemesan']; ?></b></td>
            <td><?= $p['no_hp']; ?></td>
            <td><?= $p['nama_produk']; ?></td>
            <td><?= $p['jumlah']; ?> pcs</td>
            <td><?= $p['alamat']; ?></td>
            <td><?= $p['tanggal']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Daftar Produk -->
    <h3>📋 Daftar Produk Saat Ini</h3>
    <table>
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php $no = 1; while($row = mysqli_fetch_assoc($result_produk)) : ?>
        <tr>
            <td><?= $no++; ?></td>
            <td>
                <?php if(!empty($row['foto']) && file_exists('img/' . $row['foto'])): ?>
                    <img src="img/<?= $row['foto']; ?>" width="40" height="40" style="object-fit:cover; border-radius:4px;">
                <?php else: ?>
                    <span>-</span>
                <?php endif; ?>
            </td>
            <td><?= $row['nama_produk']; ?></td>
            <td><?= $row['kategori']; ?></td>
            <td><?= $row['stok']; ?></td>
            <td>
                <a href="admin.php?edit_id=<?= $row['id']; ?>" class="btn-edit">Edit</a>
                <a href="admin.php?hapus=<?= $row['id']; ?>" class="btn-del" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>