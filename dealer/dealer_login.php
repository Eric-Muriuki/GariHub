<?php
require_once '../includes/db.php';
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("SELECT * FROM dealers WHERE email = ? AND phone = ?");
    $stmt->execute([$email, $phone]);
    $dealer = $stmt->fetch();

    if ($dealer) {
        $_SESSION['dealer_id'] = $dealer['id'];
        $_SESSION['dealer_name'] = $dealer['company_name'];
        header("Location: dealer_dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dealer Login</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .container { width: 400px; margin: 40px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2D4F2B; }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: #FFB823; color: #2D4F2B; border: none; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Dealer Login</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <button type="submit">Login</button>
        <p>No account? <a href="dealer_register.php">Register here</a></p>
    </form>
</div>
</body>
</html>
