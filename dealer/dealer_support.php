<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../dealer_login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];
$message = "";

// Handle support ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message_body = trim($_POST['message']);

    if (!empty($subject) && !empty($message_body)) {
        $stmt = $pdo->prepare("INSERT INTO support (user_type, user_id, subject, message, status) VALUES ('dealer', ?, ?, ?, 'Open')");
        if ($stmt->execute([$dealer_id, $subject, $message_body])) {
            $message = "✅ Support ticket submitted.";
        } else {
            $message = "❌ Failed to submit ticket.";
        }
    } else {
        $message = "❌ Subject and message are required.";
    }
}

// Fetch past tickets
$tickets = $pdo->prepare("SELECT * FROM support WHERE  user_id = ? ORDER BY created_at DESC");
$tickets->execute([$dealer_id]);
$rows = $tickets->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dealer Support</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
    }

    nav {
      width: 220px;
      background: #2D4F2B;
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px;
    }

    nav h2 {
      text-align: center;
      color: #FFF1CA;
    }

    nav ul {
      list-style: none;
      padding: 0;
      margin-top: 30px;
    }

    nav li {
      margin: 15px 0;
    }

    nav a {
      color: white;
      text-decoration: none;
    }

    nav a:hover {
      color: #FFB823;
    }

    .container {
      margin-left: 240px;
      padding: 30px;
    }

    h2 {
      color: #2D4F2B;
    }

    form {
      background: white;
      padding: 20px;
      max-width: 600px;
      border-radius: 8px;
    }

    input[type="text"], textarea {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    textarea {
      resize: vertical;
      height: 100px;
    }

    button {
      background-color: #2D4F2B;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      margin-top: 10px;
      cursor: pointer;
    }

    button:hover {
      background-color: #FFB823;
      color: #2D4F2B;
    }

    .message {
      padding: 10px;
      background: #e0ffe0;
      margin-top: 15px;
      border-left: 5px solid #2D4F2B;
    }

    .tickets {
      margin-top: 40px;
      max-width: 800px;
    }

    .ticket {
      background: #fff;
      padding: 15px;
      border-left: 4px solid #708A58;
      margin-bottom: 15px;
      border-radius: 6px;
    }

    .ticket h4 {
      margin: 0 0 5px 0;
      color: #2D4F2B;
    }

    .ticket small {
      color: gray;
    }

    .ticket .status {
      float: right;
      font-size: 0.9em;
      padding: 3px 6px;
      background: #ddd;
      border-radius: 4px;
    }

    .ticket .status.Open {
      background: #FFB823;
      color: #2D4F2B;
    }

    .ticket .status.Resolved {
      background: #2D4F2B;
      color: #fff;
    }
  </style>
</head>
<body>

<nav>
  <h2>🚗 Dealer Panel</h2>
  <ul>
    <li><a href="dealer_dashboard.php">Dashboard</a></li>
    <li><a href="dealer_submissions.php">Submissions</a></li>
    <li><a href="dealer_offers.php">Offers</a></li>
    <li><a href="release_upload.php">Release Upload</a></li>
    <li><a href="payment_upload.php">Payment Upload</a></li>
    <li><a href="dealer_logs.php">Activity Logs</a></li>
    <li><a href="dealer_support.php">Support</a></li>
    <li><a href="../logout.php" style="color:#FFB823;">Logout</a></li>
  </ul>
</nav>

<div class="container">
  <h2>Support</h2>

  <form method="POST">
    <label>Subject</label>
    <input type="text" name="subject" required>

    <label>Message</label>
    <textarea name="message" required></textarea>

    <button type="submit">Submit Ticket</button>
  </form>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <div class="tickets">
    <h3>Previous Tickets</h3>
    <?php if (count($rows) === 0): ?>
      <p>No tickets submitted yet.</p>
    <?php else: ?>
      <?php foreach ($rows as $t): ?>
        <div class="ticket">
          <h4><?= htmlspecialchars($t['subject']) ?>
            <span class="status <?= $t['status'] ?>"><?= $t['status'] ?></span>
          </h4>
          <p><?= nl2br(htmlspecialchars($t['message'])) ?></p>
          <?php if (!empty($t['response'])): ?>
            <hr>
            <strong>Admin Response:</strong>
            <p><?= nl2br(htmlspecialchars($t['response'])) ?></p>
          <?php endif; ?>
          <small>Submitted: <?= date("F j, Y, g:i a", strtotime($t['created_at'])) ?></small>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
