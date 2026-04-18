<?php
include 'db.php';
include 'recipe_helpers.php';

$user = require_user_login($conn);
$userId = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('myRecipes.php');
}

$recipeId = (int) ($_POST['recipeID'] ?? 0);
if ($recipeId <= 0) {
    die('Recipe ID is missing.');
}

$existingRecipe = fetch_recipe_with_details($conn, $recipeId, $userId);
if (!$existingRecipe) {
    die('Recipe not found.');
}

$name = trim($_POST['name'] ?? '');
$categoryId = (int) ($_POST['categoryID'] ?? 0);
$description = trim($_POST['description'] ?? '');
$ingredients = collect_recipe_ingredients($_POST['ingredient_name'] ?? [], $_POST['ingredient_quantity'] ?? []);
$instructions = collect_recipe_instructions($_POST['instruction_text'] ?? []);
$videoUrl = trim($_POST['videoUrl'] ?? '');

if ($name === '' || $categoryId <= 0 || $description === '' || empty($ingredients) || empty($instructions)) {
    die('Missing required recipe information.');
}

$newPhotoPath = save_uploaded_recipe_file($_FILES['photo'] ?? [], 'photos', $userId);
$newVideoPath = save_uploaded_recipe_file($_FILES['video'] ?? [], 'videos', $userId);

$photoPath = $existingRecipe['photoFileName'];
if ($newPhotoPath !== null) {
    $photoPath = $newPhotoPath;
}

$videoPath = $existingRecipe['videoFilePath'];
if ($newVideoPath !== null) {
    $videoPath = $newVideoPath;
} elseif ($videoUrl !== '') {
    $videoPath = $videoUrl;
}

$conn->begin_transaction();

try {
    $recipeStmt = $conn->prepare("
        UPDATE recipe
        SET categoryID = ?, name = ?, description = ?, photoFileName = ?, videoFilePath = ?
        WHERE id = ? AND userID = ?
    ");
    $recipeStmt->bind_param("issssii", $categoryId, $name, $description, $photoPath, $videoPath, $recipeId, $userId);
    $recipeStmt->execute();
    $recipeStmt->close();

    $deleteIngredientsStmt = $conn->prepare("DELETE FROM recipeingredient WHERE recipeID = ?");
    $deleteIngredientsStmt->bind_param("i", $recipeId);
    $deleteIngredientsStmt->execute();
    $deleteIngredientsStmt->close();

    $deleteInstructionsStmt = $conn->prepare("DELETE FROM recipeinstruction WHERE recipeID = ?");
    $deleteInstructionsStmt->bind_param("i", $recipeId);
    $deleteInstructionsStmt->execute();
    $deleteInstructionsStmt->close();

    insert_recipe_ingredients($conn, $recipeId, $ingredients);
    insert_recipe_instructions($conn, $recipeId, $instructions);

    $conn->commit();

    if ($newPhotoPath !== null) {
        delete_local_recipe_file($existingRecipe['photoFileName']);
    }

    if ($newVideoPath !== null || ($videoUrl !== '' && $existingRecipe['videoFilePath'] !== $videoUrl)) {
        delete_local_recipe_file($existingRecipe['videoFilePath']);
    }

    redirect_to('myRecipes.php');
} catch (Throwable $e) {
    $conn->rollback();
    if ($newPhotoPath !== null) {
        delete_local_recipe_file($newPhotoPath);
    }
    if ($newVideoPath !== null) {
        delete_local_recipe_file($newVideoPath);
    }
    die('Failed to update recipe: ' . $e->getMessage());
}
