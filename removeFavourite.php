<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

if (isset($_GET['recipeID'])) {
    $recipeID = (int) $_GET['recipeID'];

    $stmt = $conn->prepare("DELETE FROM favourites WHERE userID = ? AND recipeID = ?");
    $stmt->bind_param("ii", $user_id, $recipeID);
    $stmt->execute();
}

header("Location: user.php");
exit();
?>
