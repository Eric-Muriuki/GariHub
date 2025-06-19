<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../dealer_login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];
$message = "";

// Fetch vehicles that belong to this dealer and are sold
$stmt = $pdo->prepare("
    SELECT t.id AS trade_id, v.id AS vehicle_id, v.make, v.model, v.year, u.name AS buyer
    FROM trades t
    JOIN vehicles v ON t.vehicle_id = v.id
    JOIN users u ON t.user_id = u.id
    WHERE v.dealer_id = ? AND t.status = 'Approved'
    ORDER BY t.submission_date DESC
");
$stmt->execute([$dealer_id]);
$trades = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trade_id = $_POST['trade_id'];
    $release_file = $_FILES['release']['name'];

    $targetDir = "../uploads/releases/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filePath = $targetDir . "release_doc_" . $trade_id . ".pdf";

    if (move_uploaded_file($_FILES['release']['tmp_name'], $filePath)) {
        // Log upload
        $log = $pdo->prepare("INSERT INTO logs (actor_type, actor_id, action, context) VALUES ('dealer', ?, 'Uploaded Release Document', ?)");
        $context = "Uploaded release for Trade ID: $trade_id";
        $log->execute([$dealer_id, $context]);

        $message = "✅ Release document uploaded successfully.";
    } else {
        $message = "❌ Failed to upload the document.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upload Release Document</title>
  <style>
    body {
      font-family: Arial;
      background-color: #f5f5f5;
      margin: 0;
    }

    nav {
      width: 220px;
      height: 100vh;
      background-color: #2D4F2B;
      position: fixed;
      top: 0;
      left: 0;
      color: white;
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
      margin-bottom: 20px;
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

    .form-box {
      background: white;
      padding: 20px;
      border-radius: 8px;
      max-width: 600px;
    }

    select, input[type="file"], button {
      width: 100%;
      padding: 10px;
      margin-top: 12px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      background-color: #2D4F2B;
      color: white;
      border: none;
      cursor: pointer;
    }

    button:hover {
      background-color: #FFB823;
      color: #2D4F2B;
    }

    .message {
      margin-top: 20px;
      padding: 10px;
      background: #e0ffe0;
      border-left: 5px solid #2D4F2B;
    }

    .error {
      background: #ffe0e0;
      border-left: 5px solid red;
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
  <h2>Upload Release Document</h2>

  <div class="form-box">
    <form method="POST" enctype="multipart/form-data">
      <label>Select Trade:</label>
      <select name="trade_id" required>
        <option value="">-- Choose a Completed Trade --</option>
        <?php foreach ($trades as $t): ?>
          <option value="<?= $t['trade_id'] ?>">
            <?= htmlspecialchars($t['make']) . " " . htmlspecialchars($t['model']) . " (" . $t['year'] . ") - Buyer: " . htmlspecialchars($t['buyer']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Upload Release Document (PDF Only):</label>
      <input type="file" name="release" accept=".pdf" required>

      <button type="submit">Upload</button>
    </form>

    <?php if (!empty($message)): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
