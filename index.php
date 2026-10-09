<?php
session_start();
require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

$sql = "SELECT p.id, p.name, p.price, p.stock,
               c.name AS category,
               s.name AS supplier
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN suppliers  s ON p.supplier_id = s.id
        ORDER BY p.id";

$products = $pdo->query($sql)->fetchAll();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventaris Barang</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Daftar Produk</h1>
    <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>
    <p><a class="btn" href="create.php">+ Tambah Produk</a></p> 
   <table border="1" cellpadding="8">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Supplier</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($products as $i => $p): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td><?= htmlspecialchars($p['supplier']) ?></td>
            <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
            <td><?= (int) $p['stock'] ?></td>
            <td>
    <a href="edit.php?id=<?= (int) $p['id'] ?>">Edit</a>

    <form method="POST" action="delete.php" style="display:inline"
          onsubmit="return confirm('Hapus produk ini?')">
        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
        <button type="submit">Hapus</button>
    </form>
</td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>