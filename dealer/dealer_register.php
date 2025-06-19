<?php
require_once '../includes/db.php';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = trim($_POST["company_name"]);
    $contact_person = trim($_POST["contact_person"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $location = trim($_POST["location"]);

    $pin_certificate = $_FILES['pin_certificate']['name'];
    $license_document = $_FILES['license_document']['name'];

    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $pinPath = $targetDir . basename($pin_certificate);
    $licensePath = $targetDir . basename($license_document);

    if (move_uploaded_file($_FILES['pin_certificate']['tmp_name'], $pinPath) &&
        move_uploaded_file($_FILES['license_document']['tmp_name'], $licensePath)) {
        
        $stmt = $pdo->prepare("INSERT INTO dealers (company_name, contact_person, email, phone, location, pin_certificate, license_document) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$company_name, $contact_person, $email, $phone, $location, $pin_certificate, $license_document])) {
            session_start();
            $_SESSION['dealer_id'] = $pdo->lastInsertId();
            $_SESSION['dealer_name'] = $company_name;
            header("Location: dealer_dashboard.php");
            exit;
        } else {
            $errors[] = "Failed to save dealer.";
        }
    } else {
        $errors[] = "Upload failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dealer Registration</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .container { width: 400px; margin: 40px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2D4F2B; }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: #2D4F2B; color: white; border: none; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Dealer Registration</h2>

    <?php if (!empty($errors)) foreach ($errors as $e) echo "<p class='error'>$e</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="company_name" placeholder="Company Name" required>
        <input type="text" name="contact_person" placeholder="Contact Person" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="text" name="location" placeholder="Location" required>

        <label>PIN Certificate (PDF)</label>
        <input type="file" name="pin_certificate" accept=".pdf" required>

        <label>License Document (PDF)</label>
        <input type="file" name="license_document" accept=".pdf" required>

        <button type="submit">Register</button>
        <p>Already have an account? <a href="dealer_login.php">Login</a></p>
    </form>
</div>
</body>
</html>
