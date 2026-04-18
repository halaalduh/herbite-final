<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$userID = (int) $_SESSION['user_id'];
$userType = $_SESSION['user_type'] ?? 'user';
$recipeID = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($recipeID <= 0) {
    die("Invalid recipe ID.");
}

/* current user info */
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$userResult = $stmt->get_result();
$currentUser = $userResult->fetch_assoc();
$stmt->close();

/* recipe info + creator + category */
$recipeQuery = "SELECT recipe.*,
                       users.firstName,
                       users.lastName,
                       users.photoFileName AS creatorPhoto,
                       recipecategory.categoryName
                FROM recipe
                JOIN users ON recipe.userID = users.id
                LEFT JOIN recipecategory ON recipe.categoryID = recipecategory.id
                WHERE recipe.id = ?";
$stmt = $conn->prepare($recipeQuery);
$stmt->bind_param("i", $recipeID);
$stmt->execute();
$recipeResult = $stmt->get_result();

if ($recipeResult->num_rows == 0) {
    die("Recipe not found.");
}

$recipe = $recipeResult->fetch_assoc();
$stmt->close();

/* likes count */
$likesQuery = "SELECT COUNT(*) AS totalLikes FROM likes WHERE recipeID = ?";
$stmt = $conn->prepare($likesQuery);
$stmt->bind_param("i", $recipeID);
$stmt->execute();
$likesResult = $stmt->get_result();
$likesRow = $likesResult->fetch_assoc();
$totalLikes = $likesRow['totalLikes'] ?? 0;
$stmt->close();

/* ingredients */
$ingredientsQuery = "SELECT ingredientName, quantity
                     FROM recipeingredient
                     WHERE recipeID = ?
                     ORDER BY id ASC";
$stmt = $conn->prepare($ingredientsQuery);
$stmt->bind_param("i", $recipeID);
$stmt->execute();
$ingredientsResult = $stmt->get_result();
$stmt->close();

/* instructions */
$instructionsQuery = "SELECT stepNumber, instructionText
                      FROM recipeinstruction
                      WHERE recipeID = ?
                      ORDER BY stepNumber ASC, id ASC";
$stmt = $conn->prepare($instructionsQuery);
$stmt->bind_param("i", $recipeID);
$stmt->execute();
$instructionsResult = $stmt->get_result();
$stmt->close();

/* comments */
$commentsQuery = "SELECT comment.comment,
                         comment.date,
                         users.firstName,
                         users.lastName,
                         users.photoFileName
                  FROM comment
                  JOIN users ON comment.userID = users.id
                  WHERE comment.recipeID = ?
                  ORDER BY comment.date DESC, comment.id DESC";
$stmt = $conn->prepare($commentsQuery);
$stmt->bind_param("i", $recipeID);
$stmt->execute();
$commentsResult = $stmt->get_result();
$stmt->close();

/* checks */
$likedQuery = "SELECT * FROM likes WHERE userID = ? AND recipeID = ?";
$stmt = $conn->prepare($likedQuery);
$stmt->bind_param("ii", $userID, $recipeID);
$stmt->execute();
$alreadyLiked = $stmt->get_result()->num_rows > 0;
$stmt->close();

$favQuery = "SELECT * FROM favourites WHERE userID = ? AND recipeID = ?";
$stmt = $conn->prepare($favQuery);
$stmt->bind_param("ii", $userID, $recipeID);
$stmt->execute();
$alreadyFavourite = $stmt->get_result()->num_rows > 0;
$stmt->close();

$reportQuery = "SELECT * FROM report WHERE userID = ? AND recipeID = ?";
$stmt = $conn->prepare($reportQuery);
$stmt->bind_param("ii", $userID, $recipeID);
$stmt->execute();
$alreadyReported = $stmt->get_result()->num_rows > 0;
$stmt->close();

$canInteract = ($userType != "admin" && $userID != $recipe['userID']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Recipe | HerBite</title>
  <link rel="stylesheet" href="stylesheet.css">
  <script src="Script.js"></script>
</head>
<body class="user-page view-page admin-page header-admin">
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
          <div class="welcome">Welcome <span class="name"><?php echo htmlspecialchars($currentUser['firstName']); ?></span></div>
        </div>
        <div class="logout">
          <a href="logout.php">Sign-out</a>
        </div>
      </div>
    </header>

    <main class="page-main">
      <section class="view-head">
        <div class="view-title">
          <h2><?php echo htmlspecialchars($recipe['name']); ?></h2>
        </div>

        <div class="view-actions">
          <a class="btn-small btn-ghost" href="myRecipes.php">Back</a>

          <?php if ($canInteract) { ?>
            <form action="addLike.php" method="post" style="display:inline;">
              <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
              <button class="btn-small btn-ghost" type="submit" <?php echo $alreadyLiked ? 'disabled' : ''; ?>>
                Like
              </button>
            </form>

            <form action="addFavourite.php" method="post" style="display:inline;">
              <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
              <button class="btn-small btn-ghost" type="submit" <?php echo $alreadyFavourite ? 'disabled' : ''; ?>>
                Add to favourites
              </button>
            </form>

            <form action="addReport.php" method="post" style="display:inline;">
              <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
              <button class="btn-small btn-danger" type="submit" <?php echo $alreadyReported ? 'disabled' : ''; ?>>
                Report
              </button>
            </form>
          <?php } ?>
        </div>
      </section>

      <section class="admin-card view-card">
        <h2>Recipe Overview</h2>

        <div class="overview-grid">
          <div class="overview-imgWrap">
            <img src="<?php echo htmlspecialchars(!empty($recipe['photoFileName']) ? $recipe['photoFileName'] : 'default.png'); ?>" class="overview-img" alt="<?php echo htmlspecialchars($recipe['name']); ?>">
          </div>

          <div class="overview-info">
            <div class="meta">
              <span class="meta-item">Likes: <?php echo $totalLikes; ?></span>
            </div>

            <p class="muted"><strong>Category:</strong> <?php echo htmlspecialchars($recipe['categoryName'] ?? 'Uncategorized'); ?></p>
            <p class="muted"><strong>Description:</strong> <?php echo htmlspecialchars($recipe['description']); ?></p>

            <?php if (!empty($recipe['videoFilePath'])) { ?>
              <?php if (preg_match('/^https?:\/\//i', $recipe['videoFilePath'])) { ?>
                <p class="muted">
                  <strong>Video:</strong>
                  <a class="mr-link" href="<?php echo htmlspecialchars($recipe['videoFilePath']); ?>" target="_blank">Watch video</a>
                </p>
              <?php } else { ?>
                <video width="320" controls>
                  <source src="<?php echo htmlspecialchars($recipe['videoFilePath']); ?>">
                  Your browser does not support the video tag.
                </video>
              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </section>

      <section class="admin-card view-card">
        <h2>Recipe Creator</h2>
        <div class="creator-cell">
          <img src="<?php echo htmlspecialchars(!empty($recipe['creatorPhoto']) ? $recipe['creatorPhoto'] : 'default.png'); ?>" alt="Creator photo" class="creator-avatar square">
          <div>
            <div style="font-weight:800;"><?php echo htmlspecialchars($recipe['firstName'] . ' ' . $recipe['lastName']); ?></div>
            <div class="muted small">Recipe Creator</div>
          </div>
        </div>
      </section>

      <section class="admin-card view-card">
        <h2>Ingredients</h2>
        <p class="muted">All ingredients required for this recipe.</p>

        <?php if ($ingredientsResult->num_rows > 0) { ?>
          <ul class="steps-list">
            <?php while ($ingredient = $ingredientsResult->fetch_assoc()) { ?>
              <li>
                <?php echo htmlspecialchars($ingredient['ingredientName']); ?> — <?php echo htmlspecialchars($ingredient['quantity']); ?>
              </li>
            <?php } ?>
          </ul>
        <?php } else { ?>
          <p class="muted">No ingredients added.</p>
        <?php } ?>
      </section>

      <section class="admin-card view-card">
        <h2>Preparation Steps</h2>
        <p class="muted">Quick and easy steps.</p>

        <?php if ($instructionsResult->num_rows > 0) { ?>
          <ol class="steps-list">
            <?php while ($instruction = $instructionsResult->fetch_assoc()) { ?>
              <li><?php echo htmlspecialchars($instruction['instructionText']); ?></li>
            <?php } ?>
          </ol>
        <?php } else { ?>
          <p class="muted">No preparation steps added.</p>
        <?php } ?>
      </section>

      <section class="admin-card view-card">
        <h2>Comments</h2>

        <form class="comment-form" action="addComment.php" method="post">
          <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
          <input type="text" name="comment" placeholder="Write a comment..." required>
          <button class="btn-small btn-primary" type="submit">Add Comment</button>
        </form>

        <div class="comments">
          <?php if ($commentsResult->num_rows > 0) { ?>
            <?php while ($comment = $commentsResult->fetch_assoc()) { ?>
              <div class="comment">
                <div class="comment-head">
                  <img src="<?php echo htmlspecialchars(!empty($comment['photoFileName']) ? $comment['photoFileName'] : 'default.png'); ?>" alt="User photo" class="comment-avatar">
                  <div class="comment-meta">
                    <div class="comment-name"><?php echo htmlspecialchars($comment['firstName'] . ' ' . $comment['lastName']); ?></div>
                    <div class="comment-time muted small"><?php echo htmlspecialchars($comment['date']); ?></div>
                  </div>
                </div>
                <div class="comment-body"><?php echo htmlspecialchars($comment['comment']); ?></div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <p class="muted">No comments yet.</p>
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