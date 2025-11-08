<?php
require_once __DIR__ . '/config/db_connect.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Невалідний email";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Невірний email або пароль";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Вхід — FoodFusion</title>
  <link rel="stylesheet" href="styles/auth.css">
</head>
<body>

<div class="auth-container">
  <!-- Ліва частина -->
  <div class="auth-left">
    <div class="auth-brand">
      <h1 onclick="window.location.href='index.php'">FoodFusion</h1>
      <p>Готуй, експериментуй, насолоджуйся 🍽️</p>
    </div>
  </div>

  <!-- Права частина -->
  <div class="auth-right">
    <div class="auth-box">
      <h2>Вхід</h2>

      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

        <label>Пароль</label>
        <input type="password" name="password">

        <button type="submit">Увійти</button>
      </form>

      <p class="switch-text">Немає акаунта? <a href="register.php">Реєстрація</a></p>
    </div>
  </div>
</div>

</body>
</html>
