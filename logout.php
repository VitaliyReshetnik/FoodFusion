<?php
require_once __DIR__ . '/config/db_connect.php';

// очищаємо сесію
session_unset();
session_destroy();

// повертаємо на головну
header("Location: index.php");
exit;
