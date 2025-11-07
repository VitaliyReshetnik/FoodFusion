<?php
// ===== Підключення до БД =====
try {
    $pdo = new PDO("mysql:host=localhost;dbname=foodfusion;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}

// ===== Отримуємо категорії =====
$categories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// ===== AJAX запит (повертає тільки картки) =====
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $search     = $_GET['q'] ?? '';
    $min_time   = $_GET['min_time'] ?? '';
    $max_time   = $_GET['max_time'] ?? '';
    $min_ing    = $_GET['min_ing'] ?? '';
    $max_ing    = $_GET['max_ing'] ?? '';
    $rating     = $_GET['rating'] ?? '';
    $is_vegan   = $_GET['is_vegan'] ?? '';

    $catFilter     = $_GET['category'] ?? [];
    $mealFilter    = $_GET['meal'] ?? [];
    $cuisineFilter = $_GET['cuisine'] ?? [];
    $diffFilter    = $_GET['difficulty'] ?? [];

    $sql = "SELECT DISTINCT r.id, r.title, r.image, r.cook_time, r.rating, r.difficulty, r.is_vegan
            FROM recipes r
            LEFT JOIN recipe_categories rc ON r.id = rc.recipe_id
            LEFT JOIN categories c ON rc.category_id = c.id
            WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND r.title LIKE ?";
        $params[] = "%{$search}%";
    }

    if (!empty($catFilter)) {
        $sql .= " AND c.slug IN (" . str_repeat('?,', count($catFilter) - 1) . "?)";
        $params = array_merge($params, $catFilter);
    }

    if (!empty($mealFilter)) {
        $sql .= " AND r.meal_type IN (" . str_repeat('?,', count($mealFilter) - 1) . "?)";
        $params = array_merge($params, $mealFilter);
    }

    if (!empty($cuisineFilter)) {
        $sql .= " AND r.cuisine IN (" . str_repeat('?,', count($cuisineFilter) - 1) . "?)";
        $params = array_merge($params, $cuisineFilter);
    }

    if (!empty($diffFilter)) {
        $sql .= " AND r.difficulty IN (" . str_repeat('?,', count($diffFilter) - 1) . "?)";
        $params = array_merge($params, $diffFilter);
    }

    if ($min_time !== '' && $max_time !== '') {
        $sql .= " AND r.cook_time BETWEEN ? AND ?";
        $params[] = (int)$min_time;
        $params[] = (int)$max_time;
    }

    if ($min_ing !== '' && $max_ing !== '') {
        $sql .= " AND r.ingredients_count BETWEEN ? AND ?";
        $params[] = (int)$min_ing;
        $params[] = (int)$max_ing;
    }

    if ($rating !== '' && is_numeric($rating)) {
        $sql .= " AND r.rating >= ?";
        $params[] = (float)$rating;
    }

    if ($is_vegan === 'yes') {
        $sql .= " AND r.is_vegan = 1";
    } elseif ($is_vegan === 'no') {
        $sql .= " AND r.is_vegan = 0";
    }

    $sql .= " ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$recipes) {
        echo "<p style='grid-column:1/-1;text-align:center;'>Нічого не знайдено 🥺</p>";
        exit;
    }

    foreach ($recipes as $r) {
        $img = htmlspecialchars($r['image'] ?: 'placeholder.jpg');
        $title = htmlspecialchars($r['title']);
        $time = (int)$r['cook_time'];
        $ratingVal = number_format((float)($r['rating'] ?? 0), 1);
        $diff = ucfirst($r['difficulty']);
        $badge = $r['is_vegan']
            ? "<span style='color:#27ae60;'>🌱 Веганське</span>"
            : "<span style='color:#e67e22;'>🍖 Не веганське</span>";

        echo "<div class='recipe-card'>
                <img src='assets/images/{$img}' alt='{$title}'>
                <div class='info'>
                  <h3>{$title}</h3>
                  <p>⏱ {$time} хв | {$diff}</p>
                  <p>⭐ {$ratingVal} / 5</p>
                  <p>{$badge}</p>
                </div>
                <button class='details-btn' onclick=\"location.href='recipe.php?id={$r['id']}'\">Детальніше</button>
              </div>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Пошук рецептів | FoodFusion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/nav.css">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/footer.css">
  <link rel="stylesheet" href="styles/cards.css">
</head>
<body>

<header>
  <div class="nav-container">
    <div class="logo">
      <a href="index.html"><img src="assets/images/FoodFusion.png" alt="FoodFusion"></a>
    </div>
    <nav>
      <ul>
        <li><a href="search.php" class="active">Пошук рецептів</a></li>
        <li><a href="calculator.html">Калькулятор калорій</a></li>
        <li><a href="shopping-list.html">Список покупок</a></li>
        <li><a href="profile.html">Профіль</a></li>
      </ul>
    </nav>
  </div>
</header>

<main>
  <section class="search-bar">
    <form id="searchForm">
      <input type="text" name="q" placeholder="Пошук рецептів...">
      <button type="button" id="filterBtn">⚙️ Фільтри</button>
      <button type="submit">🔍 Знайти</button>
    </form>
  </section>

  <div class="overlay"></div>

  <div class="filter-popup" id="filterPopup">
    <h2>Фільтри</h2>
    <div class="filter-columns">

      <div class="filter-col">
        <h3>Кухня</h3>
        <label><input form="searchForm" type="checkbox" name="cuisine[]" value="Українська"> Українська</label>
        <label><input form="searchForm" type="checkbox" name="cuisine[]" value="Італійська"> Італійська</label>
        <label><input form="searchForm" type="checkbox" name="cuisine[]" value="Азійська"> Азійська</label>
        <label><input form="searchForm" type="checkbox" name="cuisine[]" value="Американська"> Американська</label>
      </div>

      <div class="filter-col">
        <h3>Категорії</h3>
        <?php foreach ($categories as $cat): ?>
          <label>
            <input form="searchForm" type="checkbox" name="category[]" value="<?= htmlspecialchars($cat['slug']) ?>">
            <?= htmlspecialchars($cat['name']) ?>
          </label>
        <?php endforeach; ?>

        <h3>Прийом їжі</h3>
        <label><input form="searchForm" type="checkbox" name="meal[]" value="breakfast"> Сніданок</label>
        <label><input form="searchForm" type="checkbox" name="meal[]" value="lunch"> Обід</label>
        <label><input form="searchForm" type="checkbox" name="meal[]" value="dinner"> Вечеря</label>
        <label><input form="searchForm" type="checkbox" name="meal[]" value="snack"> Перекус</label>
      </div>

      <div class="filter-col">
        <h3>Тип страви</h3>
        <label><input form="searchForm" type="radio" name="is_vegan" value="yes"> Веганське 🌱</label>
        <label><input form="searchForm" type="radio" name="is_vegan" value="no"> Не веганське 🍖</label>
        <label><input form="searchForm" type="radio" name="is_vegan" value=""> Неважливо</label>

        <h3>Складність</h3>
        <label><input form="searchForm" type="checkbox" name="difficulty[]" value="easy"> Легко</label>
        <label><input form="searchForm" type="checkbox" name="difficulty[]" value="medium"> Середньо</label>
        <label><input form="searchForm" type="checkbox" name="difficulty[]" value="hard"> Важко</label>
      </div>

      <div class="filter-col">
        <h3>⏱️ Час приготування (хв)</h3>
        <div class="range-value"><span id="timeMinVal">0</span> – <span id="timeMaxVal">120</span></div>
        <div class="range-slider" id="timeRange">
          <div class="slider-track"></div>
          <div class="slider-range" id="timeTrack"></div>
          <input form="searchForm" type="range" id="timeMin" name="min_time" min="0" max="120" value="0" step="5">
          <input form="searchForm" type="range" id="timeMax" name="max_time" min="0" max="120" value="120" step="5">
        </div>

        <h3>🥣 Кількість інгредієнтів</h3>
        <div class="range-value"><span id="ingMinVal">1</span> – <span id="ingMaxVal">20</span></div>
        <div class="range-slider" id="ingRange">
          <div class="slider-track"></div>
          <div class="slider-range" id="ingTrack"></div>
          <input form="searchForm" type="range" id="ingMin" name="min_ing" min="1" max="20" value="1" step="1">
          <input form="searchForm" type="range" id="ingMax" name="max_ing" min="1" max="20" value="20" step="1">
        </div>

        <h3>⭐ Мінімальний рейтинг</h3>
        <div class="stars">
          <input form="searchForm" type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
          <input form="searchForm" type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
          <input form="searchForm" type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
          <input form="searchForm" type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
          <input form="searchForm" type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
        </div>
      </div>
    </div>

    <div class="filter-actions">
      <button type="submit" form="searchForm">Застосувати</button>
      <button type="reset" id="resetBtn">Скинути</button>
      <button type="button" id="closeFilters">Закрити</button>
    </div>
  </div>

  <section id="recipes" class="recipes-container"></section>
</main>

<footer>
  <div class="footer-container">
    <p>© 2025 FoodFusion. Усі права захищено.</p>
  </div>
</footer>

<script src="scripts/search.js"></script>
</body>
</html>
