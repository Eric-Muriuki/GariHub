<?php
require_once 'includes/db.php';
session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Email already registered.";
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $phone, $hashed])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header("Location: user/user_dashboard.php");
            exit();
        } else {
            $errors[] = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration - GariHub</title>
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
    <h2>Create User Account</h2>
    <?php foreach ($errors as $e) echo "<p class='error'>$e</p>"; ?>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>
        <button class="btn" type="submit">Register</button>
        <p>Already have an account? <a href="user_login.php">Login</a></p>
    </form>
</div>
</body>
</html>
