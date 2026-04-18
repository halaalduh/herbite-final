<?php
session_start();
include 'db.php';

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];

$checkUser = $conn->query("SELECT * FROM users WHERE emailAddress='$email'");
$checkBlocked = $conn->query("SELECT * FROM blockeduser WHERE emailAddress='$email'");

if ($checkUser->num_rows > 0 || $checkBlocked->num_rows > 0) {
    header("Location: signup.html?error=emailexists");
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$photoFileName = "default.png";

$sql = "INSERT INTO users (userType, firstName, lastName, emailAddress, password, photoFileName)
        VALUES ('user', '$firstName', '$lastName', '$email', '$hashedPassword', '$photoFileName')";

if ($conn->query($sql) === TRUE) {
    $newUserId = $conn->insert_id;

    if (isset($_FILES['profileImg']) && $_FILES['profileImg']['error'] == 0) {

    $originalName = basename($_FILES['profileImg']['name']);
    $photoFileName = $newUserId . "_" . $originalName;

    $path = __DIR__ . "/images/" . $photoFileName;

    move_uploaded_file($_FILES['profileImg']['tmp_name'], $path);

    $conn->query("UPDATE users SET photoFileName='$photoFileName' WHERE id=$newUserId");
}

    $_SESSION['user_id'] = $newUserId;
    $_SESSION['user_type'] = 'user';

    header("Location: user.php");
    exit();
} else {
    die("sql error: " . $conn->error);
}
?>

