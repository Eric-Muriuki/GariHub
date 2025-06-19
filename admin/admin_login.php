<?php
session_start();
require_once '../includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $errors[] = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        .form-container { max-width: 400px; margin: 60px auto; background: white; padding: 20px; border-radius: 8px; }
        input, button { width: 100%; padding: 10px; margin: 8px 0; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Admin Login</h2>
    <?php foreach ($errors as $e): ?>
        <p class="error"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>No account? <a href="admin_register.php">Register here</a></p>
    </form>
</div>
</body>
</html>
