<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != "admin") {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportID = (int) $_POST['reportID'];
    $creatorID = (int) $_POST['creatorID'];
    $action = $_POST['action'];

    if ($action == "dismiss") {
        $deleteReport = "DELETE FROM report WHERE id = ?";
        $stmt = $conn->prepare($deleteReport);
        $stmt->bind_param("i", $reportID);
        $stmt->execute();
        $stmt->close();
    }

    if ($action == "block") {

        /* get user */
        $userQuery = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($userQuery);
        $stmt->bind_param("i", $creatorID);
        $stmt->execute();
        $userResult = $stmt->get_result();

        if ($userResult->num_rows > 0) {
            $user = $userResult->fetch_assoc();
            $stmt->close();

            /* add to blocked users */
            $insertBlocked = "INSERT INTO blockeduser (firstName, lastName, emailAddress)
                              VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertBlocked);
            $stmt->bind_param("sss", $user['firstName'], $user['lastName'], $user['emailAddress']);
            $stmt->execute();
            $stmt->close();

            /* get user recipes first for deleting files */
            $recipesQuery = "SELECT photoFileName, videoFilePath FROM recipe WHERE userID = ?";
            $stmt = $conn->prepare($recipesQuery);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $recipesResult = $stmt->get_result();

            while ($recipe = $recipesResult->fetch_assoc()) {
                if (!empty($recipe['photoFileName']) && file_exists($recipe['photoFileName'])) {
                    unlink($recipe['photoFileName']);
                }

                if (!empty($recipe['videoFilePath']) && !preg_match('/^https?:\/\//i', $recipe['videoFilePath']) && file_exists($recipe['videoFilePath'])) {
                    unlink($recipe['videoFilePath']);
                }
            }
            $stmt->close();

            /* delete ingredients */
            $deleteIngredients = "DELETE recipeingredient
                                  FROM recipeingredient
                                  JOIN recipe ON recipeingredient.recipeID = recipe.id
                                  WHERE recipe.userID = ?";
            $stmt = $conn->prepare($deleteIngredients);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete instructions */
            $deleteInstructions = "DELETE recipeinstruction
                                   FROM recipeinstruction
                                   JOIN recipe ON recipeinstruction.recipeID = recipe.id
                                   WHERE recipe.userID = ?";
            $stmt = $conn->prepare($deleteInstructions);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete reports */
            $deleteReports = "DELETE report
                              FROM report
                              JOIN recipe ON report.recipeID = recipe.id
                              WHERE recipe.userID = ?";
            $stmt = $conn->prepare($deleteReports);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete likes */
            $deleteLikes = "DELETE likes
                            FROM likes
                            JOIN recipe ON likes.recipeID = recipe.id
                            WHERE recipe.userID = ?";
            $stmt = $conn->prepare($deleteLikes);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete favourites */
            $deleteFavs = "DELETE favourites
                           FROM favourites
                           JOIN recipe ON favourites.recipeID = recipe.id
                           WHERE recipe.userID = ?";
            $stmt = $conn->prepare($deleteFavs);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete comments */
            $deleteComments = "DELETE comment
                               FROM comment
                               JOIN recipe ON comment.recipeID = recipe.id
                               WHERE recipe.userID = ?";
            $stmt = $conn->prepare($deleteComments);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete recipes */
            $deleteRecipes = "DELETE FROM recipe WHERE userID = ?";
            $stmt = $conn->prepare($deleteRecipes);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete all reports made by this user too */
            $deleteUserReports = "DELETE FROM report WHERE userID = ?";
            $stmt = $conn->prepare($deleteUserReports);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();

            /* delete user */
            $deleteUser = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($deleteUser);
            $stmt->bind_param("i", $creatorID);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: admin.php");
    exit();
}
?>