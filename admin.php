<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
    header("Location: login.html");
    exit();
}

$adminID = (int) $_SESSION['user_id'];

/* Get admin info */
$adminQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($adminQuery);
$stmt->bind_param("i", $adminID);
$stmt->execute();
$adminResult = $stmt->get_result();

if ($adminResult->num_rows == 0) {
    die("Admin not found.");
}

$admin = $adminResult->fetch_assoc();
$stmt->close();

/* Get reported recipes */
$reportsQuery = "SELECT report.id AS reportID,
                        recipe.id AS recipeID,
                        recipe.name AS recipeName,
                        users.id AS creatorID,
                        users.firstName,
                        users.lastName,
                        users.photoFileName
                 FROM report
                 JOIN recipe ON report.recipeID = recipe.id
                 JOIN users ON recipe.userID = users.id";
$reportsResult = $conn->query($reportsQuery);

/* Get blocked users */
$blockedQuery = "SELECT * FROM blockeduser";
$blockedResult = $conn->query($blockedQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | HerBite</title>
  <link rel="stylesheet" href="stylesheet.css">
</head>
<body class="admin-page">
<div class="page">

  <header class="site-header">
    <div class="header-inner">

      <a href="index.php" class="home-link" aria-label="Go to home">
        <img src="home.PNG" alt="Home">
      </a>

      <div class="brand">
        <img src="logo.jpg" alt="HerBite Logo">
      </div>

      <div class="brand-title">
        <img src="title.jpg" alt="HerBite Title">
      </div>

      <div class="header-right">
        <div class="welcome">Welcome <span class="name"><?php echo htmlspecialchars($admin['firstName']); ?></span></div>
      </div>

      <div class="logout">
        <a href="logout.php">Sign-out</a>
      </div>
    </div>
  </header>

  <main class="page-main">

    <section class="admin-card admin-info-card">
      <div class="admin-big-box">
        <h2>My Information</h2>
        <div class="admin-info">
          <div class="label">Name</div>
          <div class="value">
            <?php echo htmlspecialchars($admin['firstName'] . " " . $admin['lastName']); ?>
          </div>

          <div class="label">Email address</div>
          <div class="value">
            <?php echo htmlspecialchars($admin['emailAddress']); ?>
          </div>
        </div>
      </div>
    </section>

    <section class="admin-card">
      <h2>Reported Recipes</h2>

      <?php if ($reportsResult && $reportsResult->num_rows > 0) { ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Recipe Name</th>
            <th>Recipe Creator</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <?php while ($report = $reportsResult->fetch_assoc()) { ?>
          <tr>
            <td>
              <a href="viewRecipe.php?id=<?php echo $report['recipeID']; ?>">
                <?php echo htmlspecialchars($report['recipeName']); ?>
              </a>
            </td>

            <td>
              <div class="creator-cell">
                <img src="<?php echo htmlspecialchars(!empty($report['photoFileName']) ? $report['photoFileName'] : 'default.png'); ?>" alt="Creator photo" class="creator-avatar square">
                <span class="name">
                  <?php echo htmlspecialchars($report['firstName'] . " " . $report['lastName']); ?>
                </span>
              </div>
            </td>

            <td>
              <form class="action-form" action="handleReportAction.php" method="post">
                <input type="hidden" name="reportID" value="<?php echo $report['reportID']; ?>">
                <input type="hidden" name="recipeID" value="<?php echo $report['recipeID']; ?>">
                <input type="hidden" name="creatorID" value="<?php echo $report['creatorID']; ?>">

                <label>
                  <input type="radio" name="action" value="block" required> Block User
                </label>
                <label>
                  <input type="radio" name="action" value="dismiss" required> Dismiss Report
                </label>
                <button type="submit" class="btn-small">Submit</button>
              </form>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
        <p>No reports found.</p>
      <?php } ?>
    </section>

    <section class="admin-card">
      <h2>Blocked Users List</h2>

      <?php if ($blockedResult && $blockedResult->num_rows > 0) { ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email Address</th>
          </tr>
        </thead>

        <tbody>
          <?php while ($blocked = $blockedResult->fetch_assoc()) { ?>
          <tr>
            <td><?php echo htmlspecialchars($blocked['firstName'] . " " . $blocked['lastName']); ?></td>
            <td><?php echo htmlspecialchars($blocked['emailAddress']); ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
        <p>No blocked users found.</p>
      <?php } ?>
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