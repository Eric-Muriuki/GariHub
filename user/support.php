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

// Handle support message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($subject) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO support (user_id, subject, message, created_at) VALUES (?, ?, ?, NOW())");
        if ($stmt->execute([$user_id, $subject, $message])) {
            $success = "Your message was submitted successfully.";
        } else {
            $error = "Failed to send message.";
        }
    } else {
        $error = "All fields are required.";
    }
}

// Fetch user's past support messages
$fetch = $pdo->prepare("SELECT * FROM support WHERE user_id = ? ORDER BY created_at DESC");
$fetch->execute([$user_id]);
$messages = $fetch->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Support - GariHub</title>
  <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
    }
    .main-content {
        margin-left: 240px;
        padding: 30px;
    }
    .form-box {
        background: white;
        padding: 20px;
        max-width: 600px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    h2, h3 {
        color: #2D4F2B;
    }
    input, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        margin-bottom: 15px;
        border-radius: 6px;
        border: 1px solid #ccc;
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
    .success {
        background-color: #e3f9e3;
        color: #2e7d32;
    }
    .error {
        background-color: #fde2e2;
        color: #b00020;
    }
    .message-box {
        background: #fff;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #708A58;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-radius: 6px;
    }
    .message-box p {
        margin: 5px 0;
    }
    .response {
        margin-top: 10px;
        background: #f9f9f9;
        padding: 10px;
        border-left: 3px solid #FFB823;
        border-radius: 5px;
    }
  </style>
</head>
<body>

<div class="main-content">
    <h2>Support Center</h2>

    <div class="form-box">
        <h3>Submit a Ticket</h3>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Subject:</label>
            <input type="text" name="subject" required>

            <label>Message:</label>
            <textarea name="message" rows="5" required></textarea>

            <button type="submit" class="btn">Submit</button>
        </form>
    </div>

    <h3>Your Previous Tickets</h3>

    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message-box">
                <p><strong><?= htmlspecialchars($msg['subject']) ?></strong></p>
                <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                <small><em>Submitted: <?= htmlspecialchars($msg['created_at']) ?></em></small>

                <?php if (!empty($msg['response'])): ?>
                    <div class="response">
                        <strong>Admin Response:</strong>
                        <p><?= nl2br(htmlspecialchars($msg['response'])) ?></p>
                    </div>
                <?php else: ?>
                    <div class="response"><em>Awaiting response...</em></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You haven’t submitted any support requests yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
