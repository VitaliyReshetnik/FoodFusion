<?php
session_start();
require_once __DIR__ . "/config/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo->prepare("DELETE FROM history WHERE user_id = ?")
    ->execute([$_SESSION['user_id']]);

header("Location: history.php");
exit;
