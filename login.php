<?php
require_once 'includes/db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION['user_id'] = $user["id"];
        $_SESSION['user_name'] = $user["name"];
        header("Location: user/dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login - GariHub</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        .form-container { max-width: 400px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        .btn { background: #2D4F2B; color: white; border: none; border-radius: 5px; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>User Login</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="btn" type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
</div>
</body>
</html>
