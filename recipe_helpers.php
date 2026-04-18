<?php

function redirect_to(string $path): void {
    header("Location: " . $path);
    exit();
}

function require_user_login(mysqli $conn): array {
    session_start();

    if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'user') {
        redirect_to('login.html');
    }

    $userId = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, firstName, lastName, emailAddress, photoFileName FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        session_unset();
        session_destroy();
        redirect_to('login.html');
    }

    $_SESSION['first_name'] = $user['firstName'];
    return $user;
}

function h(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function ensure_recipe_upload_dir(string $type): string {
    $dir = __DIR__ . '/uploads/recipes/' . $type;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    return $dir;
}

function save_uploaded_recipe_file(array $file, string $type, int $userId): ?string {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $originalName = basename($file['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowedByType = [
        'photos' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'videos' => ['mp4', 'mov', 'webm', 'ogg']
    ];

    if (!in_array($extension, $allowedByType[$type] ?? [], true)) {
        return null;
    }

    $directory = ensure_recipe_upload_dir($type);
    $safeName = uniqid('recipe_' . $userId . '_', true) . '.' . $extension;
    $absolutePath = $directory . '/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
        return null;
    }

    return 'uploads/recipes/' . $type . '/' . $safeName;
}

function delete_local_recipe_file(?string $relativePath): void {
    if (empty($relativePath)) {
        return;
    }

    if (preg_match('#^https?://#i', $relativePath)) {
        return;
    }

    $absolutePath = __DIR__ . '/' . ltrim($relativePath, '/');
    if (is_file($absolutePath)) {
        unlink($absolutePath);
    }
}

function collect_recipe_ingredients(array $names, array $quantities): array {
    $ingredients = [];

    $count = max(count($names), count($quantities));
    for ($i = 0; $i < $count; $i++) {
        $name = trim($names[$i] ?? '');
        $quantity = trim($quantities[$i] ?? '');

        if ($name === '' && $quantity === '') {
            continue;
        }

        if ($name === '' || $quantity === '') {
            continue;
        }

        $ingredients[] = [
            'name' => $name,
            'quantity' => $quantity
        ];
    }

    return $ingredients;
}

function collect_recipe_instructions(array $steps): array {
    $instructions = [];

    foreach ($steps as $step) {
        $text = trim($step ?? '');
        if ($text !== '') {
            $instructions[] = $text;
        }
    }

    return $instructions;
}

function insert_recipe_ingredients(mysqli $conn, int $recipeId, array $ingredients): void {
    if (empty($ingredients)) {
        return;
    }

    $stmt = $conn->prepare("INSERT INTO recipeingredient (recipeID, ingredientName, quantity) VALUES (?, ?, ?)");
    foreach ($ingredients as $ingredient) {
        $stmt->bind_param("iss", $recipeId, $ingredient['name'], $ingredient['quantity']);
        $stmt->execute();
    }
    $stmt->close();
}

function insert_recipe_instructions(mysqli $conn, int $recipeId, array $instructions): void {
    if (empty($instructions)) {
        return;
    }

    $stmt = $conn->prepare("INSERT INTO recipeinstruction (recipeID, stepNumber, instructionText) VALUES (?, ?, ?)");
    foreach ($instructions as $index => $instruction) {
        $stepNumber = $index + 1;
        $stmt->bind_param("iis", $recipeId, $stepNumber, $instruction);
        $stmt->execute();
    }
    $stmt->close();
}

function fetch_recipe_with_details(mysqli $conn, int $recipeId, int $userId): ?array {
    $stmt = $conn->prepare("
        SELECT recipe.*,
               recipecategory.categoryName
        FROM recipe
        LEFT JOIN recipecategory ON recipecategory.id = recipe.categoryID
        WHERE recipe.id = ? AND recipe.userID = ?
    ");
    $stmt->bind_param("ii", $recipeId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();
    $stmt->close();

    if (!$recipe) {
        return null;
    }

    $ingredientsStmt = $conn->prepare("
        SELECT id, ingredientName, quantity
        FROM recipeingredient
        WHERE recipeID = ?
        ORDER BY id ASC
    ");
    $ingredientsStmt->bind_param("i", $recipeId);
    $ingredientsStmt->execute();
    $recipe['ingredients'] = $ingredientsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $ingredientsStmt->close();

    $instructionsStmt = $conn->prepare("
        SELECT id, stepNumber, instructionText
        FROM recipeinstruction
        WHERE recipeID = ?
        ORDER BY stepNumber ASC, id ASC
    ");
    $instructionsStmt->bind_param("i", $recipeId);
    $instructionsStmt->execute();
    $recipe['instructions'] = $instructionsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $instructionsStmt->close();

    return $recipe;
}
