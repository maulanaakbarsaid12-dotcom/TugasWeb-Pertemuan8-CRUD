<?php
session_start();
require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();
$jumlah = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

echo "Koneksi berhasil! Jumlah produk: " . $jumlah;