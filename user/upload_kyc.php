<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['kyc_file'])) {
    $file = $_FILES['kyc_file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

    if (in_array(strtolower($ext), $allowed)) {
        $filename = "kyc_user_" . $user_id . "_" . time() . "." . $ext;
        $uploadPath = "../uploads/kyc/" . $filename;

        if (!file_exists("../uploads/kyc/")) {
            mkdir("../uploads/kyc/", 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Save to DB
            $stmt = $pdo->prepare("UPDATE users SET kyc_document = ? WHERE id = ?");
            if ($stmt->execute([$filename, $user_id])) {
                $success = "KYC document uploaded successfully!";
            } else {
                $error = "Failed to save to database.";
            }
        } else {
            $error = "Failed to move file.";
        }
    } else {
        $error = "Invalid file type. Only PDF, JPG, PNG allowed.";
    }
}

// Fetch current file if any
$stmt = $pdo->prepare("SELECT kyc_document FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload KYC Document</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f5f5f5;
        }
        .main-content {
            margin-left: 240px;
            padding: 30px;
        }
        .form-box {
            background: white;
            padding: 25px;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2D4F2B;
        }
        .btn {
            padding: 10px 20px;
            background-color: #708A58;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .success { background: #e3f9e3; color: #2e7d32; }
        .error { background: #fde2e2; color: #b00020; }
    </style>
</head>
<body>

<div class="main-content">
    <h2>Upload KYC Document</h2>

    <div class="form-box">
        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($current): ?>
            <p><strong>Current Document:</strong>
                <a href="../uploads/kyc/<?= htmlspecialchars($current) ?>" target="_blank">View Uploaded File</a>
            </p>
        <?php else: ?>
            <p><strong>Status:</strong> No KYC document uploaded.</p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label>Select KYC File (PDF, JPG, PNG):</label><br><br>
            <input type="file" name="kyc_file" required><br><br>
            <button type="submit" class="btn">Upload Document</button>
        </form>
    </div>
</div>

</body>
</html>
