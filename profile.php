<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';

// якщо користувач не увійшов — перенаправляємо на логін
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Мій профіль — FoodFusion</title>

  <!-- базові стилі -->
  <link rel="stylesheet" href="styles/nav.css">
  <link rel="stylesheet" href="styles/footer.css">
  <link rel="stylesheet" href="styles/styles.css">
  <!-- індивідуальні стилі для профілю -->
  <link rel="stylesheet" href="styles/profile.css">
</head>
<body>

<!-- ===== Навігація ===== -->
<header>
  <div class="nav-container">
    <!-- Лого -->
    <div class="logo">
      <a href="index.php">
        <img src="assets/images/FoodFusion.png" alt="FoodFusion Logo">
      </a>
    </div>

    <!-- Меню -->
    <nav>
      <ul>
        <li><a href="search.php">Пошук рецептів</a></li>
        <li><a href="calculator.php">Калькулятор калорій</a></li>
        <li><a href="shopping_list.php">Список покупок</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Профіль -->
          <li class="profile-menu">
            <button type="button" class="profile-btn">
              <img src="assets/images/avatar.png" alt="Profile" />
              <span><?= htmlspecialchars($_SESSION['user_name']) ?: 'Профіль' ?></span>
            </button>

            <ul class="dropdown">
              <li><a href="profile.php">Мій профіль</a></li>
              <li><a href="favorites.php">Вподобані</a></li>
              <li><a href="collections.php">Колекції рецептів</a></li>
              <li><a href="history.php">Історія</a></li>
              <li><a href="logout.php">Вийти</a></li>
            </ul>
          </li>
        <?php else: ?>
          <!-- Якщо користувач не увійшов -->
          <li><a href="login.php" class="login-btn">Увійти</a></li>
          <li><a href="register.php" class="register-btn">Реєстрація</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

<!-- ===== Контент профілю ===== -->
<main>
  <section class="profile-wrapper">
    <div class="profile-header">
      <div class="profile-avatar">
        <img src="assets/images/default-avatar.png" alt="User Avatar">
      </div>
      <div class="profile-info">
        <h1>Привіт, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h1>
        <p>Email: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
      </div>
    </div>

    <div class="profile-settings">
      <h2>Налаштування акаунта</h2>

      <form class="profile-form" method="post" action="#">
        <label>Ім’я користувача</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>">

        <label>Новий пароль</label>
        <input type="password" name="new_password" placeholder="Введіть новий пароль">

        <label>Підтвердження пароля</label>
        <input type="password" name="confirm_password" placeholder="Повторіть пароль">

        <button type="submit" class="save-btn">Зберегти зміни</button>
      </form>
    </div>
  </section>
</main>

<!-- ===== Футер ===== -->
<footer>
  <p>© 2025 FoodFusion. All rights reserved.</p>
</footer>

<!-- ===== JS ===== -->
<script src="scripts/profile-menu.js"></script>
</body>
</html>
