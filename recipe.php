<?php
require_once __DIR__ . "/config/db_connect.php";

$id = $_GET['id'] ?? null;
$recipe = null;
$ingredients = [];

if ($id) {
    // ===== Отримуємо рецепт =====
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recipe) {
        // ===== Отримуємо категорії =====
        $catStmt = $pdo->prepare("
            SELECT c.name 
            FROM recipe_categories rc
            JOIN categories c ON rc.category_id = c.id
            WHERE rc.recipe_id = ?
        ");
        $catStmt->execute([$id]);
        $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
        $recipe['categories'] = $categories;

        // ===== Отримуємо інгредієнти =====
        $ingStmt = $pdo->prepare("
            SELECT i.name, ri.amount
            FROM recipe_ingredients ri
            JOIN ingredients i ON ri.ingredient_id = i.id
            WHERE ri.recipe_id = ?
            ORDER BY ri.sort_order ASC
        ");
        $ingStmt->execute([$id]);
        $ingredients = $ingStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $recipe ? htmlspecialchars($recipe['title']) : "Рецепт не знайдено" ?> | FoodFusion</title>

  <link rel="stylesheet" href="styles/nav.css">
  <link rel="stylesheet" href="styles/footer.css">
  <link rel="stylesheet" href="styles/recipe.css">
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

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
        <li><a href="#">Калькулятор калорій</a></li>
        <li><a href="#">Список покупок</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="profile.php">Профіль</a></li>
          <li><a href="logout.php" class="logout-btn">Вийти</a></li>
        <?php else: ?>
          <li><a href="login.php">Увійти</a></li>
          <li><a href="register.php">Реєстрація</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

<main class="recipe-container">
  <?php if ($recipe): ?>
  <article class="recipe-card">
    <div class="recipe-top">
      <div class="recipe-image">
        <img src="assets/images/<?= htmlspecialchars($recipe['image']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>">
      </div>

      <div class="recipe-info">
        <h1 class="recipe-title"><?= htmlspecialchars($recipe['title']) ?></h1>

        <div class="recipe-meta">
          <?php if (!empty($recipe['categories'])): ?>
            <div class="categories">
              <?php foreach ($recipe['categories'] as $cat): ?>
                <span class="category-tag">#<?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="category">Без категорії</p>
          <?php endif; ?>

          <p class="time">⏱️ <?= htmlspecialchars($recipe['cook_time'] ?? '?') ?> хв</p>
          <?php if (!empty($recipe['calories'])): ?>
            <p class="calories">🔥 <?= htmlspecialchars($recipe['calories']) ?> ккал</p>
          <?php endif; ?>
        </div>

        <p class="description"><?= nl2br(htmlspecialchars($recipe['description'] ?? '')) ?></p>

        <div class="recipe-actions">
          <button class="favorite-btn">❤️ Додати у вподобані</button>
          <button class="shopping-btn">🛒 Додати у список покупок</button>
        </div>
      </div>
    </div>

    <!-- ===== Блок інгредієнтів ===== -->
    <section class="ingredients-block">
      <h2>🧂 Інгредієнти</h2>
      <?php if (!empty($ingredients)): ?>
        <ul class="ingredient-list">
          <?php foreach ($ingredients as $ing): ?>
            <li><span><?= htmlspecialchars($ing['name']) ?></span> — <?= htmlspecialchars($ing['amount']) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="no-ingredients">Інгредієнти будуть додані пізніше.</p>
      <?php endif; ?>
    </section>

    <!-- ===== Основний рецепт ===== -->
    <div class="recipe-body">
      <h2>🍳 Приготування</h2>
      <p><?= nl2br(htmlspecialchars($recipe['instructions'] ?? 'Опис приготування буде додано пізніше.')) ?></p>
    </div>

    <!-- Заглушки -->
    <div class="placeholder">
      <h3>❤️ Вподобання</h3>
      <p>Ця функція буде доступна пізніше — ви зможете зберігати свої улюблені рецепти.</p>
    </div>

    <div class="placeholder comments">
      <h3>💬 Коментарі</h3>
      <p>Тут з'являться коментарі користувачів після авторизації.</p>
    </div>
  </article>

  <?php else: ?>
  <div class="not-found">
    <h2>Рецепт не знайдено 😕</h2>
    <a href="index.html" class="back-link">Повернутись на головну</a>
  </div>
  <?php endif; ?>
</main>

<footer>
  <div class="footer-container">
    <p>&copy; <?= date("Y") ?> FoodFusion. Усі права захищено.</p>
  </div>
</footer>

</body>
</html>
