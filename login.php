
<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$blocked = $conn->query("SELECT * FROM blockeduser WHERE emailAddress='$email'");

if ($blocked->num_rows > 0) {
    header("Location: login.html?error=blocked");
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE emailAddress='$email'");

if ($result->num_rows == 0) {
    header("Location: login.html?error=wrongemail");
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    header("Location: login.html?error=wrongpassword");
    exit();
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_type'] = $user['userType'];

if ($user['userType'] == 'admin') {
    header("Location: admin.php");
} else {
    header("Location: user.php");
}
exit();
?>