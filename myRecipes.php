<?php
include 'db.php';
include 'recipe_helpers.php';

$user = require_user_login($conn);
$userId = (int) $user['id'];

$recipesStmt = $conn->prepare("
    SELECT recipe.id,
           recipe.name,
           recipe.photoFileName,
           recipe.videoFilePath,
           recipecategory.categoryName,
           COALESCE(like_counts.totalLikes, 0) AS totalLikes
    FROM recipe
    LEFT JOIN recipecategory ON recipecategory.id = recipe.categoryID
    LEFT JOIN (
        SELECT recipeID, COUNT(*) AS totalLikes
        FROM likes
        GROUP BY recipeID
    ) AS like_counts ON like_counts.recipeID = recipe.id
    WHERE recipe.userID = ?
    ORDER BY recipe.id DESC
");
$recipesStmt->bind_param("i", $userId);
$recipesStmt->execute();
$recipesResult = $recipesStmt->get_result();
$recipes = $recipesResult->fetch_all(MYSQLI_ASSOC);
$recipesStmt->close();

$recipeIds = array_column($recipes, 'id');
$ingredientsByRecipe = [];
$instructionsByRecipe = [];

if (!empty($recipeIds)) {
    $idList = implode(',', array_map('intval', $recipeIds));

    $ingredientsResult = $conn->query("
        SELECT recipeID, ingredientName, quantity
        FROM recipeingredient
        WHERE recipeID IN ($idList)
        ORDER BY id ASC
    ");

    while ($row = $ingredientsResult->fetch_assoc()) {
        $ingredientsByRecipe[(int) $row['recipeID']][] = $row;
    }

    $instructionsResult = $conn->query("
        SELECT recipeID, stepNumber, instructionText
        FROM recipeinstruction
        WHERE recipeID IN ($idList)
        ORDER BY recipeID ASC, stepNumber ASC, id ASC
    ");

    while ($row = $instructionsResult->fetch_assoc()) {
        $instructionsByRecipe[(int) $row['recipeID']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Recipes | HerBite</title>
  <link rel="stylesheet" href="stylesheet.css">
</head>
<body class="user-page header-admin">
  <div class="page">
    <header class="site-header">
      <div class="header-inner">
        <a href="user.php" class="home-link" aria-label="Go to home">
          <img src="home.PNG" alt="Home">
        </a>
        <div class="brand">
          <img src="logo.jpg" alt="HerBite Logo">
        </div>
        <div class="brand-title">
          <img src="title.jpg" alt="HerBite Title">
        </div>
        <div class="header-right">
          <div class="welcome">Welcome <span class="name"><?php echo h($user['firstName']); ?></span></div>
        </div>
        <div class="logout">
          <a href="logout.php">Sign-out</a>
        </div>
      </div>
    </header>

    <main class="page-main">
      <section class="box mr-wrap">
        <div class="mr-head">
          <div>
            <h2 class="mr-title">My Recipes</h2>
            <p class="mr-sub">View and manage the recipes you added.</p>
          </div>

          <a href="addRecipe.php" class="mr-add">+ Add New Recipe</a>
        </div>

        <div class="mr-card">
          <div class="mr-card-top">
            <h3>Your Added Recipes</h3>
          </div>

          <?php if (!empty($recipes)) { ?>
            <div class="mr-table-scroll">
              <table class="mr-table">
                <thead>
                  <tr>
                    <th>Recipe</th>
                    <th>Ingredients</th>
                    <th>Instructions</th>
                    <th>Video</th>
                    <th>Likes</th>
                    <th>Edit</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recipes as $recipe) {
                      $recipeId = (int) $recipe['id'];
                      $ingredients = $ingredientsByRecipe[$recipeId] ?? [];
                      $instructions = $instructionsByRecipe[$recipeId] ?? [];
                  ?>
                    <tr>
                      <td>
                        <a class="mr-recipe" href="viewRecipe.html?id=<?php echo $recipeId; ?>">
                          <span class="mr-thumb">
                            <img src="<?php echo h($recipe['photoFileName'] ?: 'default.png'); ?>" alt="<?php echo h($recipe['name']); ?>">
                          </span>
                          <span class="mr-meta">
                            <span class="mr-name"><?php echo h($recipe['name']); ?></span>
                            <span class="mr-cat">Category: <?php echo h($recipe['categoryName'] ?: 'Uncategorized'); ?></span>
                          </span>
                        </a>
                      </td>
                      <td>
                        <?php if (!empty($ingredients)) { ?>
                          <ul class="mr-list">
                            <?php foreach ($ingredients as $ingredient) { ?>
                              <li><?php echo h($ingredient['ingredientName']); ?> — <?php echo h($ingredient['quantity']); ?></li>
                            <?php } ?>
                          </ul>
                        <?php } else { ?>
                          <span class="mr-muted">No ingredients added.</span>
                        <?php } ?>
                      </td>
                      <td>
                        <?php if (!empty($instructions)) { ?>
                          <ol class="mr-list">
                            <?php foreach ($instructions as $instruction) { ?>
                              <li><?php echo h($instruction['instructionText']); ?></li>
                            <?php } ?>
                          </ol>
                        <?php } else { ?>
                          <span class="mr-muted">No instructions added.</span>
                        <?php } ?>
                      </td>
                      <td>
                        <?php if (!empty($recipe['videoFilePath'])) { ?>
                          <a class="mr-link" href="<?php echo h($recipe['videoFilePath']); ?>" target="_blank" rel="noopener noreferrer">Watch video</a>
                        <?php } else { ?>
                          <span class="mr-muted">No video for recipe</span>
                        <?php } ?>
                      </td>
                      <td><span class="mr-pill"><?php echo (int) $recipe['totalLikes']; ?></span></td>
                      <td><a class="mr-btn mr-btn-edit" href="editRecipe.php?id=<?php echo $recipeId; ?>">Edit</a></td>
                      <td><a class="mr-btn mr-btn-del" href="deleteRecipe.php?id=<?php echo $recipeId; ?>" onclick="return confirm('Delete this recipe?');">Delete</a></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          <?php } else { ?>
            <p class="mr-muted">You have not added any recipes yet.</p>
          <?php } ?>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="footer-inner">
        <div class="footer-col">
          <h4>Services</h4>
          <p>Healthy Recipes</p>
          <p>Quick Meals</p>
          <p>Balanced Plates</p>
        </div>
        <div class="footer-col">
          <h4>Locations</h4>
          <p>Riyadh</p>
          <p>Jeddah</p>
          <p>Dammam</p>
        </div>
        <div class="footer-col">
          <h4>Contact Us</h4>
          <p>+966 5X XXX XXXX</p>
          <p>herbite@email.com</p>
          <p>@HerBite</p>
        </div>
      </div>
      <div class="footer-bottom">
        © 2026 HerBite. All rights reserved.
      </div>
    </footer>
  </div>
</body>
</html>
