<?php
include 'db.php';
include 'recipe_helpers.php';

$user = require_user_login($conn);
$userId = (int) $user['id'];
$recipeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($recipeId <= 0) {
    die('Recipe ID is missing.');
}

$recipe = fetch_recipe_with_details($conn, $recipeId, $userId);
if (!$recipe) {
    die('Recipe not found.');
}

$categoriesResult = $conn->query("SELECT id, categoryName FROM recipecategory ORDER BY categoryName ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Recipe | HerBite</title>
  <link rel="stylesheet" href="stylesheet.css">
  <script src="Script.js" defer></script>
</head>
<body class="user-page header-admin">
  <div class="page">
    <header class="site-header">
      <div class="header-inner">
        <a href="user.php" class="home-link" aria-label="Go to home">
          <img src="home.PNG" alt="Home">
        </a>
        <div class="brand"><img src="logo.jpg" alt="HerBite Logo"></div>
        <div class="brand-title"><img src="title.jpg" alt="HerBite Title"></div>
        <div class="header-right">
          <div class="welcome">Welcome <span class="name"><?php echo h($user['firstName']); ?></span></div>
        </div>
        <div class="logout"><a href="logout.php">Sign-out</a></div>
      </div>
    </header>

    <main class="page-main">
      <div class="er-page-head">
        <div>
          <h1 class="er-h1">Edit Recipe</h1>
          <p class="er-p">Update the steps below to edit your recipe.</p>
        </div>
        <a class="er-back" href="myRecipes.php">← Back</a>
      </div>

      <section class="er-card">
        <div class="er-card-top">
          <h2 class="er-card-title">Recipe Form</h2>
          <div class="er-card-sub"></div>
        </div>

        <div class="er-card-body">
          <div class="er-tabs" role="tablist" aria-label="Recipe steps">
            <button type="button" class="er-tab is-active" data-step="0">1. Basic Info</button>
            <button type="button" class="er-tab" data-step="1">2. Ingredients</button>
            <button type="button" class="er-tab" data-step="2">3. Instructions</button>
            <button type="button" class="er-tab" data-step="3">4. Video</button>
          </div>

          <form id="editRecipeForm" action="updateRecipe.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="recipeID" value="<?php echo $recipeId; ?>">

            <div class="er-step is-active" data-step="0">
              <div class="er-grid2">
                <div>
                  <label class="er-label" for="editRecipeName">Recipe Name</label>
                  <input class="er-input" id="editRecipeName" name="name" type="text" value="<?php echo h($recipe['name']); ?>" required>
                </div>

                <div>
                  <label class="er-label" for="editCategoryID">Category</label>
                  <select class="er-input" id="editCategoryID" name="categoryID" required>
                    <?php while ($category = $categoriesResult->fetch_assoc()) { ?>
                      <option value="<?php echo (int) $category['id']; ?>" <?php echo ((int) $category['id'] === (int) $recipe['categoryID']) ? 'selected' : ''; ?>>
                        <?php echo h($category['categoryName']); ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <label class="er-label" for="editDescription">Description</label>
              <textarea class="er-textarea" id="editDescription" name="description" required><?php echo h($recipe['description']); ?></textarea>

              <div class="er-current">
                <div class="er-current-box">
                  <div class="er-current-title">Current Photo</div>
                  <img class="er-current-img" src="<?php echo h($recipe['photoFileName'] ?: 'default.png'); ?>" alt="Current photo">
                </div>

                <div class="er-current-box">
                  <div class="er-current-title">Change Photo</div>
                  <input class="er-input" name="photo" type="file" accept="image/*">
                </div>
              </div>
            </div>

            <div class="er-step" data-step="1">
              <div class="er-section-title">Ingredients</div>
              <div id="ingredientsWrap" class="er-repeat">
                <?php if (!empty($recipe['ingredients'])) { ?>
                  <?php foreach ($recipe['ingredients'] as $ingredient) { ?>
                    <div class="er-row">
                      <div>
                        <label class="er-mini">Ingredient Name</label>
                        <input class="er-input" name="ingredient_name[]" type="text" value="<?php echo h($ingredient['ingredientName']); ?>" required>
                      </div>
                      <div>
                        <label class="er-mini">Quantity</label>
                        <input class="er-input" name="ingredient_quantity[]" type="text" value="<?php echo h($ingredient['quantity']); ?>" required>
                      </div>
                    </div>
                  <?php } ?>
                <?php } else { ?>
                  <div class="er-row">
                    <div>
                      <label class="er-mini">Ingredient Name</label>
                      <input class="er-input" name="ingredient_name[]" type="text" required>
                    </div>
                    <div>
                      <label class="er-mini">Quantity</label>
                      <input class="er-input" name="ingredient_quantity[]" type="text" required>
                    </div>
                  </div>
                <?php } ?>
              </div>

              <button type="button" class="er-add" id="addIngredientBtn">+ Add another ingredient</button>
            </div>

            <div class="er-step" data-step="2">
              <div class="er-section-title">Instructions</div>
              <div id="stepsWrap" class="er-repeat">
                <?php if (!empty($recipe['instructions'])) { ?>
                  <?php foreach ($recipe['instructions'] as $instruction) { ?>
                    <div class="er-row1">
                      <label class="er-mini">Step <?php echo (int) $instruction['stepNumber']; ?></label>
                      <input class="er-input" name="instruction_text[]" type="text" value="<?php echo h($instruction['instructionText']); ?>" required>
                    </div>
                  <?php } ?>
                <?php } else { ?>
                  <div class="er-row1">
                    <label class="er-mini">Step 1</label>
                    <input class="er-input" name="instruction_text[]" type="text" required>
                  </div>
                <?php } ?>
              </div>
              <button type="button" class="er-add" id="addStepBtn">+ Add another step</button>
            </div>

            <div class="er-step" data-step="3">
              <div class="er-section-title">Video (Optional)</div>
              <div class="er-current">
                <div class="er-current-box">
                  <div class="er-current-title">Current Video</div>
                  <?php if (!empty($recipe['videoFilePath'])) { ?>
                    <a class="mr-link" href="<?php echo h($recipe['videoFilePath']); ?>" target="_blank" rel="noopener noreferrer">Open current video</a>
                  <?php } else { ?>
                    <div class="er-muted">No video for recipe</div>
                  <?php } ?>
                </div>

                <div class="er-current-box">
                  <div class="er-current-title">Upload Video</div>
                  <input class="er-input" name="video" type="file" accept="video/*">
                </div>
              </div>

              <label class="er-label" for="editVideoUrl">OR Video URL</label>
              <input class="er-input" id="editVideoUrl" name="videoUrl" type="url" placeholder="https://..." value="<?php echo preg_match('#^https?://#i', (string) $recipe['videoFilePath']) ? h($recipe['videoFilePath']) : ''; ?>">
            </div>

            <div class="er-actions">
              <button type="button" class="er-btn er-btn-ghost" id="prevBtn" disabled>Previous</button>
              <div class="er-actions-right">
                <button type="button" class="er-btn er-btn-primary" id="nextBtn">Next</button>
                <button type="submit" class="er-btn er-btn-primary" id="submitBtn">Update</button>
              </div>
            </div>
          </form>
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
