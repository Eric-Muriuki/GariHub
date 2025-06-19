<?php
require_once '../includes/db.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Email already registered.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            header("Location: admin_login.php");
            exit();
        } else {
            $errors[] = "Failed to register admin.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Register</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .form-container { max-width: 400px; margin: 40px auto; background: white; padding: 20px; border-radius: 8px; }
        input, button { width: 100%; padding: 10px; margin: 8px 0; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Admin Registration</h2>
    <?php foreach ($errors as $e): ?>
        <p class="error"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
        <p>Already an admin? <a href="admin_login.php">Login here</a></p>
    </form>
</div>
</body>
</html>
