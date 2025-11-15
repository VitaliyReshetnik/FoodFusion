<?php
session_start();
require_once __DIR__ . "/config/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    // Якщо користувач не залогінений — відправимо на логін
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$recipe_id = isset($_GET['recipe_id']) ? (int)$_GET['recipe_id'] : 0;

if ($recipe_id <= 0) {
    // якщо немає id рецепта — просто назад на головну
    header("Location: index.php");
    exit;
}

// Перевіряємо, чи вже є в таблиці favorites
$check = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
$check->execute([$user_id, $recipe_id]);

if ($check->rowCount() > 0) {
    // Вже є → видаляємо
    $del = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $del->execute([$user_id, $recipe_id]);
} else {
    // Нема → додаємо
    $add = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
    $add->execute([$user_id, $recipe_id]);
}

// Повертаємось на попередню сторінку, якщо можливо
$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: " . $back);
exit;
