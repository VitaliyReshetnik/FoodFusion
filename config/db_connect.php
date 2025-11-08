<?php
// ===== Запуск сесії =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== Підключення до бази даних =====
try {
    $pdo = new PDO("mysql:host=localhost;dbname=foodfusion;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}
?>
