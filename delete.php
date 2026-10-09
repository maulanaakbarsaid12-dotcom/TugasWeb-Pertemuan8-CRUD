<?php
session_start();
require_once 'config/database.php';

// Hanya terima POST. Kalau dibuka langsung lewat URL, kembali ke daftar.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$pdo = Database::getInstance()->getConnection();

$id = (int) ($_POST['id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produk berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Produk tidak ditemukan.'];
}

header("Location: index.php");
exit;