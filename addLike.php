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

    $checkQuery = "SELECT * FROM likes WHERE userID = ? AND recipeID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $userID, $recipeID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 0) {
        $query = "INSERT INTO likes (userID, recipeID) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userID, $recipeID);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: viewRecipe.php?id=" . $recipeID);
    exit();
}
?>