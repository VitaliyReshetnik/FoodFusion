<?php
session_start();
require_once __DIR__ . "/config/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.*
    FROM favorites f
    JOIN recipes r ON r.id = f.recipe_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вподобані рецепти | FoodFusion</title>

  <link rel="stylesheet" href="styles/nav.css">
  <link rel="stylesheet" href="styles/footer.css">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/cards.css">      <!-- загальні стилі карток -->
  <link rel="stylesheet" href="styles/favorites.css">  <!-- легкі стилі саме для цієї сторінки -->
</head>

<body>

<!-- ===== Навігація ===== -->
<header>
  <div class="nav-container">

    <div class="logo">
      <a href="index.php">
        <img src="assets/images/FoodFusion.png" alt="FoodFusion Logo">
      </a>
    </div>

    <nav>
      <ul>
        <li><a href="search.php">Пошук рецептів</a></li>
        <li><a href="calculator.php">Калькулятор калорій</a></li>
        <li><a href="shopping_list.php">Список покупок</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="profile-menu">
            <button type="button" class="profile-btn">
              <img src="assets/images/avatar.png" alt="Profile" />
              <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
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
          <li><a href="login.php" class="login-btn">Увійти</a></li>
          <li><a href="register.php" class="register-btn">Реєстрація</a></li>
        <?php endif; ?>
      </ul>
    </nav>

  </div>
</header>

<main>

    <!-- Заголовок сторінки -->
    <section class="favorites-header">
        <h1 class="favorites-title">💖 Вподобані рецепти</h1>

        <?php if (empty($favorites)): ?>
            <p class="no-favorites">Тут поки порожньо. Додайте свої улюблені рецепти ❤️</p>
        <?php endif; ?>
    </section>

    <?php if (!empty($favorites)): ?>
        <!-- Той самий контейнер, що і в search.php -->
        <section class="recipes-container">
            <?php foreach ($favorites as $r): ?>
                <?php
                    $img = htmlspecialchars($r['image'] ?: 'placeholder.jpg');
                    $title = htmlspecialchars($r['title']);
                    $time = (int)$r['cook_time'];
                    $ratingVal = number_format((float)($r['rating'] ?? 0), 1);
                    $diff = ucfirst($r['difficulty'] ?? '');
                    $badge = ($r['is_vegan'])
                        ? "<span style='color:#27ae60;'>🌱 Веганське</span>"
                        : "<span style='color:#e67e22;'>🍖 Не веганське</span>";
                ?>

                <div class="recipe-card">
                    <img src="assets/images/<?= $img ?>" alt="<?= $title ?>">

                    <div class="info">
                      <h3><?= $title ?></h3>
                      <p>⏱ <?= $time ?> хв | <?= $diff ?></p>
                      <p>⭐ <?= $ratingVal ?> / 5</p>
                      <p><?= $badge ?></p>
                    </div>

                    <button class="details-btn"
                        onclick="location.href='recipe.php?id=<?= $r['id'] ?>'">
                        Детальніше
                    </button>
                </div>

            <?php endforeach; ?>
        </section>
    <?php endif; ?>

</main>

<!-- ===== Футер ===== -->
<footer>
  <div class="footer-container">
    <p>&copy; <?= date("Y") ?> FoodFusion. Усі права захищено.</p>
  </div>
</footer>

<script src="scripts/profile-menu.js"></script>

</body>
</html>
