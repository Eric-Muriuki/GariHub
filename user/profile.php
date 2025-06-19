<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

$success = "";
$error = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    $params = [$email, $phone];
    $sql = "UPDATE users SET email = ?, phone = ?";

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $hashed;
    }

    $sql .= " WHERE id = ?";
    $params[] = $user_id;

    $update = $pdo->prepare($sql);
    if ($update->execute($params)) {
        $success = "Profile updated successfully.";
    } else {
        $error = "Failed to update profile.";
    }

    // Refresh user data
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f2f2;
            margin: 0;
        }
        .main-content {
            margin-left: 240px;
            padding: 30px;
        }
        .profile-box {
            background: white;
            padding: 20px;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2D4F2B;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            color: #555;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
        }
        .btn {
            padding: 10px 18px;
            background-color: #708A58;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-secondary {
            background: #FFB823;
            color: #2D4F2B;
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
    <h2>User Profile</h2>

    <div class="profile-box">
        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
        <p><strong>KYC Document:</strong>
            <?php if (!empty($user['kyc_document'])): ?>
                <a href="../uploads/kyc/<?= htmlspecialchars($user['kyc_document']) ?>" target="_blank">View Document</a>
            <?php else: ?>
                Not uploaded
            <?php endif; ?>
        </p>

        <hr><br>

        <h3>Update Profile</h3>
        <form method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            </div>

            <div class="form-group">
                <label>New Password (leave blank to keep current):</label>
                <input type="password" name="password">
            </div>

            <button type="submit" class="btn">Update</button>
            <a href="upload_kyc.php" class="btn btn-secondary" style="margin-left:10px;">Upload KYC</a>
        </form>
    </div>
</div>

</body>
</html>
