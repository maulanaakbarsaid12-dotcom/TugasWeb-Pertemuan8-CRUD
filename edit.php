<?php
session_start();
require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

// 1. Ambil id dari URL, paksa jadi angka
$id = (int) ($_GET['id'] ?? 0);

// 2. Ambil data lama dari database (prepared statement)
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
} catch (PDOException $e) {
    error_log('DB Error (edit/select): ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Terjadi kesalahan database.'];
    header("Location: index.php");
    exit;
}

// Kalau produk tidak ada, kembali ke daftar
if (!$product) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Produk tidak ditemukan.'];
    header("Location: index.php");
    exit;
}

// Data untuk isi dropdown
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$suppliers  = $pdo->query("SELECT id, name FROM suppliers ORDER BY name")->fetchAll();

// Nilai awal form = data lama dari database (pre-filled)
$name        = $product['name'];
$category_id = (int) $product['category_id'];
$supplier_id = (int) $product['supplier_id'];
$price       = $product['price'];
$stock       = $product['stock'];
$errors      = [];

// 3. Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $supplier_id = (int) ($_POST['supplier_id'] ?? 0);
    $price       = $_POST['price'] ?? '';
    $stock       = $_POST['stock'] ?? 0;

    // ---- Validasi ----
    if ($name === '') {
        $errors['name'] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($name) > 150) {
        $errors['name'] = 'Nama produk maksimal 150 karakter.';
    }

    if ($category_id < 1) {
        $errors['category_id'] = 'Pilih kategori.';
    }

    if ($supplier_id < 1) {
        $errors['supplier_id'] = 'Pilih supplier.';
    }

    if (!is_numeric($price) || $price < 0) {
        $errors['price'] = 'Harga harus berupa angka 0 atau lebih.';
    }

    if (filter_var($stock, FILTER_VALIDATE_INT) === false || $stock < 0) {
        $errors['stock'] = 'Stok harus bilangan bulat 0 atau lebih.';
    }

    // ---- Simpan hanya kalau tidak ada error ----
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "UPDATE products
                 SET name = ?, category_id = ?, supplier_id = ?, price = ?, stock = ?
                 WHERE id = ?"
            );
            $stmt->execute([$name, $category_id, $supplier_id, (float) $price, (int) $stock, $id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produk berhasil diperbarui.'];
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            error_log('DB Error (edit/update): ' . $e->getMessage());
            $errors['umum'] = 'Terjadi kesalahan saat menyimpan perubahan. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventaris Barang</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Edit Produk</h1>

    <?php if (isset($errors['umum'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['umum']) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= (int) $id ?>">
        <p>
            <label>Nama Produk<br>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($name) ?>">
            </label>
            <?php if (isset($errors['name'])): ?>
                <br><span class="error"><?= htmlspecialchars($errors['name']) ?></span>
            <?php endif; ?>
        </p>
        <p>
            <label>Kategori<br>
                <select name="category_id" required>
                    <option value="">-- Pilih kategori --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"
                            <?= $c['id'] == $category_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errors['category_id'])): ?>
                <br><span class="error"><?= htmlspecialchars($errors['category_id']) ?></span>
            <?php endif; ?>
        </p>
        <p>
            <label>Supplier<br>
                <select name="supplier_id" required>
                    <option value="">-- Pilih supplier --</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= (int) $s['id'] ?>"
                            <?= $s['id'] == $supplier_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errors['supplier_id'])): ?>
                <br><span class="error"><?= htmlspecialchars($errors['supplier_id']) ?></span>
            <?php endif; ?>
        </p>
        <p>
            <label>Harga (Rp)<br>
                <input type="number" name="price" min="0" step="0.01" required
                       value="<?= htmlspecialchars((string) $price) ?>">
            </label>
            <?php if (isset($errors['price'])): ?>
                <br><span class="error"><?= htmlspecialchars($errors['price']) ?></span>
            <?php endif; ?>
        </p>
        <p>
            <label>Stok<br>
                <input type="number" name="stock" min="0" required
                       value="<?= htmlspecialchars((string) $stock) ?>">
            </label>
            <?php if (isset($errors['stock'])): ?>
                <br><span class="error"><?= htmlspecialchars($errors['stock']) ?></span>
            <?php endif; ?>
        </p>
        <button type="submit">Perbarui</button>
        <a href="index.php">Batal</a>
    </form>
</body>
</html>