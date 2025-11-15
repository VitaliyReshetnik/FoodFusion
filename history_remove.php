<?php
session_start();
require_once __DIR__ . "/config/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$recipe_id = $_GET['id'] ?? 0;

$pdo->prepare("DELETE FROM history WHERE user_id = ? AND recipe_id = ?")
    ->execute([$_SESSION['user_id'], $recipe_id]);

header("Location: history.php");
exit;
