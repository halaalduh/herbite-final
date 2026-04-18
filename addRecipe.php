<?php
include 'db.php';
include 'recipe_helpers.php';

$user = require_user_login($conn);
$categoriesResult = $conn->query("SELECT id, categoryName FROM recipecategory ORDER BY categoryName ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Recipe | HerBite</title>
  <link rel="stylesheet" href="stylesheet.css">
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
      <div class="ar-page-head">
        <div>
          <h1 class="ar-h1">Add New Recipe</h1>
          <p class="ar-p">Complete the steps below to add your recipe.</p>
        </div>
        <a class="ar-back" href="myRecipes.php">← Back</a>
      </div>

      <section class="ar-card">
        <div class="ar-card-top">
          <h2 class="ar-card-title">Recipe Form</h2>
          <div class="ar-card-sub">Step-by-step</div>
        </div>

        <div class="ar-card-body">
          <div class="ar-tabs" role="tablist" aria-label="Recipe steps">
            <button type="button" class="ar-tab is-active" data-step="0">1. Basic Info</button>
            <button type="button" class="ar-tab" data-step="1">2. Ingredients</button>
            <button type="button" class="ar-tab" data-step="2">3. Instructions</button>
            <button type="button" class="ar-tab" data-step="3">4. Video</button>
          </div>

          <form id="addRecipeForm" action="saveRecipe.php" method="post" enctype="multipart/form-data" novalidate>
            <div class="ar-step is-active" data-step="0">
              <div class="ar-grid2">
                <div>
                  <label class="ar-label" for="recipeName">Recipe Name</label>
                  <input class="ar-input" id="recipeName" name="name" type="text" placeholder="e.g., Healthy Chicken Salad" data-label="Recipe name" required>
                </div>

                <div>
                  <label class="ar-label" for="categoryID">Category</label>
                  <select class="ar-input" id="categoryID" name="categoryID" data-label="Category" required>
                    <option value="" selected disabled>Select category</option>
                    <?php while ($category = $categoriesResult->fetch_assoc()) { ?>
                      <option value="<?php echo (int) $category['id']; ?>"><?php echo h($category['categoryName']); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <label class="ar-label" for="description">Description</label>
              <textarea class="ar-textarea" id="description" name="description" placeholder="Short description..." data-label="Description" required></textarea>

              <label class="ar-label" for="photo">Recipe Photo</label>
              <input class="ar-input" id="photo" name="photo" type="file" accept="image/*" data-label="Recipe photo" required>
            </div>

            <div class="ar-step" data-step="1">
              <div class="ar-section-title">Ingredients</div>
              <div id="ingredientsWrap" class="ar-repeat">
                <div class="ar-row">
                  <div>
                    <label class="ar-mini">Ingredient Name</label>
                    <input class="ar-input" name="ingredient_name[]" type="text" data-label="Ingredient name" required>
                  </div>
                  <div>
                    <label class="ar-mini">Quantity</label>
                    <input class="ar-input" name="ingredient_quantity[]" type="text" data-label="Ingredient quantity" required>
                  </div>
                </div>
              </div>
              <button type="button" class="ar-add" id="addIngredientBtn">+ Add another ingredient</button>
            </div>

            <div class="ar-step" data-step="2">
              <div class="ar-section-title">Instructions</div>
              <div id="stepsWrap" class="ar-repeat">
                <div class="ar-row1">
                  <label class="ar-mini">Step 1</label>
                  <input class="ar-input" name="instruction_text[]" type="text" data-label="Instruction step" required>
                </div>
              </div>
              <button type="button" class="ar-add" id="addStepBtn">+ Add another step</button>
            </div>

            <div class="ar-step" data-step="3">
              <div class="ar-section-title">Video (Optional)</div>
              <label class="ar-label" for="video">Upload Video</label>
              <input class="ar-input" id="video" name="video" type="file" accept="video/*">

              <label class="ar-label" for="videoUrl">OR Video URL</label>
              <input class="ar-input" id="videoUrl" name="videoUrl" type="url" placeholder="https://...">
            </div>

            <div class="ar-actions">
              <button type="button" class="ar-btn ar-btn-ghost" id="prevBtn" disabled>Previous</button>
              <div class="ar-actions-right">
                <button type="button" class="ar-btn ar-btn-primary" id="nextBtn">Next</button>
                <button type="submit" class="ar-btn ar-btn-primary" id="submitBtn">Submit</button>
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

  <script src="Script.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.getElementById("addRecipeForm");
      if (!form) return;

      form.addEventListener("submit", function (event) {
        const requiredFields = form.querySelectorAll("input[required], select[required], textarea[required]");

        for (const field of requiredFields) {
          const isFile = field.type === "file";
          const isEmpty = isFile ? field.files.length === 0 : field.value.trim() === "";

          if (isEmpty) {
            event.preventDefault();
            const label = field.dataset.label || "This field";
            alert(label + " is required.");
            field.focus();
            return;
          }
        }
      });
    });
  </script>
</body>
</html>
