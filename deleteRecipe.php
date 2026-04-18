<?php
include 'db.php';
include 'recipe_helpers.php';

$user = require_user_login($conn);
$userId = (int) $user['id'];
$recipeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($recipeId <= 0) {
    redirect_to('myRecipes.php');
}

$recipe = fetch_recipe_with_details($conn, $recipeId, $userId);
if (!$recipe) {
    redirect_to('myRecipes.php');
}

$conn->begin_transaction();

try {
    $tableDeletes = [
        'recipeingredient',
        'recipeinstruction',
        'comment',
        'likes',
        'favourites',
        'report'
    ];

    foreach ($tableDeletes as $table) {
        $stmt = $conn->prepare("DELETE FROM {$table} WHERE recipeID = ?");
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $stmt->close();
    }

    $recipeStmt = $conn->prepare("DELETE FROM recipe WHERE id = ? AND userID = ?");
    $recipeStmt->bind_param("ii", $recipeId, $userId);
    $recipeStmt->execute();
    $recipeStmt->close();

    $conn->commit();

    delete_local_recipe_file($recipe['photoFileName']);
    delete_local_recipe_file($recipe['videoFilePath']);

    redirect_to('myRecipes.php');
} catch (Throwable $e) {
    $conn->rollback();
    die('Failed to delete recipe: ' . $e->getMessage());
}
