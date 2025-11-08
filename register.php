<?php
require_once __DIR__ . '/config/db_connect.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password_confirm'] ?? '';

    if ($name === '') $errors[] = "Введи ім'я";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Невалідний email";
    if (strlen($pass) < 6) $errors[] = "Пароль мінімум 6 символів";
    if ($pass !== $pass2) $errors[] = "Паролі не співпадають";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Такий email вже існує";
        }
    }

    if (empty($errors)) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        header("Location: profile.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Реєстрація — FoodFusion</title>
  <link rel="stylesheet" href="styles/auth.css">
</head>
<body>

<div class="auth-container">
  <!-- Ліва частина -->
  <div class="auth-left">
    <div class="auth-brand">
      <h1 onclick="window.location.href='index.php'">FoodFusion</h1>
      <p>Кулінарія з технологіями 💫</p>
    </div>
  </div>

  <!-- Права частина -->
  <div class="auth-right">
    <div class="auth-box">
      <h2>Створи акаунт</h2>

      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post">
        <label>Ім’я</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

        <label>Пароль</label>
        <input type="password" name="password">

        <label>Підтвердження паролю</label>
        <input type="password" name="password_confirm">

        <button type="submit">Зареєструватися</button>
      </form>

      <p class="switch-text">Вже є акаунт? <a href="login.php">Увійти</a></p>
    </div>
  </div>
</div>

</body>
</html>
