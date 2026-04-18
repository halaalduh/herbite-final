<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipeID = (int) $_POST['recipeID'];
    $userID = (int) $_SESSION['user_id'];
    $commentText = trim($_POST['comment']);
    $date = date("Y-m-d H:i:s");

    if ($commentText != "") {
        $query = "INSERT INTO comment (recipeID, userID, comment, date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $recipeID, $userID, $commentText, $date);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: viewRecipe.php?id=" . $recipeID);
    exit();
}
?>