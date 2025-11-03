<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
  $pdo = new PDO(
    'mysql:host=db;port=3306;dbname=cimscat;charset=utf8mb4',
    'root',
    'rootpass',
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
  );
  echo "✅ Connexió correcta a la base de dades!";
} catch (Throwable $e) {
  echo "❌ ERROR: " . $e->getMessage();
}
