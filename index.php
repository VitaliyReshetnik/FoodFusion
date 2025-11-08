<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FoodFusion</title>

  <!-- ===== CSS ===== -->
  <link rel="stylesheet" href="styles/nav.css">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/footer.css">
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
  <!-- ===== Вітальний блок ===== -->
  <section class="banner">
    <div class="banner-text">
      <?php if (isset($_SESSION['user_name'])): ?>
        <h1>Вітаємо, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h1>
        <p>Раді бачити тебе знову у FoodFusion!</p>
      <?php else: ?>
        <h1>Welcome to FoodFusion</h1>
        <p>Discover, cook and enjoy your favorite recipes every day 🍽️</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ===== Про сайт ===== -->
  <section class="about">
    <div class="about-image">
      <img src="assets/images/about.png" alt="Cooking at home">
    </div>
    <div class="about-text">
      <h2>About FoodFusion</h2>
      <p>
        FoodFusion — це місце, де кулінарія зустрічається з технологіями. 
        Ми створюємо платформу, яка допоможе вам знаходити рецепти, 
        зберігати улюблені страви та експериментувати на кухні. 
        Просто введіть інгредієнти — і ми знайдемо, що можна приготувати прямо зараз!
      </p>
    </div>
  </section>

  <!-- ===== Explore Section ===== -->
  <section class="explore">
    <div class="explore-text">
      <h2>Explore More</h2>
      <p>
        Відкрийте для себе тисячі нових рецептів, кулінарні поради та 
        авторські ідеї від спільноти FoodFusion. Ми допоможемо вам 
        зробити приготування їжі простішим, цікавішим і смачнішим!
      </p>
    </div>
    <div class="explore-image">
      <img src="assets/images/explore.png" alt="Explore recipes">
    </div>
  </section>

  <!-- ===== Футер ===== -->
  <footer>
    <p>© 2025 FoodFusion. All rights reserved.</p>
  </footer>

  <!-- ===== JS ===== -->
  <script src="scripts/profile-menu.js"></script>
</body>
</html>
