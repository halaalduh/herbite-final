<?php
include 'db.php';
include 'recipe_helpers.php';

$user = require_user_login($conn);
$userId = (int) $user['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('addRecipe.php');
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

$photoPath = save_uploaded_recipe_file($_FILES['photo'] ?? [], 'photos', $userId);
if ($photoPath === null) {
    die('Recipe photo is required.');
}

$videoPath = save_uploaded_recipe_file($_FILES['video'] ?? [], 'videos', $userId);
if ($videoPath === null && $videoUrl !== '') {
    $videoPath = $videoUrl;
}

$conn->begin_transaction();

try {
    $idResult = $conn->query("SELECT COALESCE(MAX(id), 0) + 1 AS nextId FROM recipe");
    $nextIdRow = $idResult->fetch_assoc();
    $recipeId = (int) $nextIdRow['nextId'];

    $recipeStmt = $conn->prepare("
        INSERT INTO recipe (id, userID, categoryID, name, description, photoFileName, videoFilePath)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $recipeStmt->bind_param("iiissss", $recipeId, $userId, $categoryId, $name, $description, $photoPath, $videoPath);
    $recipeStmt->execute();
    $recipeStmt->close();

    insert_recipe_ingredients($conn, $recipeId, $ingredients);
    insert_recipe_instructions($conn, $recipeId, $instructions);

    $conn->commit();
    redirect_to('myRecipes.php');
} catch (Throwable $e) {
    $conn->rollback();
    delete_local_recipe_file($photoPath);
    if ($videoPath !== $videoUrl) {
        delete_local_recipe_file($videoPath);
    }
    die('Failed to save recipe: ' . $e->getMessage());
}
