<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../dealer_login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];
$message = "";
$selected_trade_id = $_POST['trade_id'] ?? ($_GET['trade_id'] ?? null);

// Fetch accepted offers made by this dealer
$stmt = $pdo->prepare("
    SELECT o.trade_id, v.make, v.model, v.year, u.name AS buyer
    FROM offers o
    JOIN trades t ON o.trade_id = t.id
    JOIN vehicles v ON t.vehicle_id = v.id
    JOIN users u ON t.user_id = u.id
    WHERE o.dealer_id = ? AND o.status = 'Accepted'
    ORDER BY t.submission_date DESC
");
$stmt->execute([$dealer_id]);
$accepted_offers = $stmt->fetchAll();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['release'])) {
    $trade_id = $_POST['trade_id'];
    $release_file = $_FILES['release']['name'];

    $targetDir = "../uploads/releases/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filePath = $targetDir . "release_doc_" . $trade_id . ".pdf";

    if (move_uploaded_file($_FILES['release']['tmp_name'], $filePath)) {
        // Insert or update into releases table
        $check = $pdo->prepare("SELECT * FROM releases WHERE trade_id = ?");
        $check->execute([$trade_id]);

        if ($check->rowCount()) {
            $update = $pdo->prepare("UPDATE releases SET file_path = ?, uploaded_at = NOW() WHERE trade_id = ?");
            $update->execute([$filePath, $trade_id]);
        } else {
            $insert = $pdo->prepare("INSERT INTO releases (trade_id, file_path) VALUES (?, ?)");
            $insert->execute([$trade_id, $filePath]);
        }

        // Log action
        $log = $pdo->prepare("INSERT INTO logs (actor_type, actor_id, action, context) VALUES ('dealer', ?, 'Uploaded Release Document', ?)");
        $log->execute([$dealer_id, "Release document uploaded for Trade ID $trade_id"]);

        $message = "✅ Release document uploaded successfully.";
    } else {
        $message = "❌ Failed to upload the document.";
    }

    $selected_trade_id = $trade_id;
}

// Check if release doc exists
$releasePath = $selected_trade_id ? "../uploads/releases/release_doc_{$selected_trade_id}.pdf" : null;
$docExists = $releasePath && file_exists($releasePath);
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
      margin-bottom: 30px;
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

    .pdf-viewer {
      margin-top: 30px;
    }

    iframe {
      width: 100%;
      height: 600px;
      border: none;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
  <h2>Upload / View Release Document</h2>

  <div class="form-box">
    <form method="POST" enctype="multipart/form-data">
      <label>Select Accepted Offer:</label>
      <select name="trade_id" onchange="this.form.submit()" required>
        <option value="">-- Choose Accepted Offer --</option>
        <?php foreach ($accepted_offers as $offer): ?>
          <option value="<?= $offer['trade_id'] ?>" <?= $selected_trade_id == $offer['trade_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($offer['make']) . " " . htmlspecialchars($offer['model']) . " (" . $offer['year'] . ") - Buyer: " . htmlspecialchars($offer['buyer']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <?php if ($selected_trade_id): ?>
        <label>Upload New Release Document (PDF Only):</label>
        <input type="file" name="release" accept=".pdf">
        <button type="submit">Upload / Replace Document</button>
      <?php endif; ?>
    </form>

    <?php if (!empty($message)): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>

  <?php if ($docExists): ?>
    <div class="pdf-viewer">
      <h3>Viewing Release Document</h3>
      <iframe src="<?= $releasePath ?>"></iframe>
    </div>
  <?php elseif ($selected_trade_id): ?>
    <p>No release document found for selected trade.</p>
  <?php endif; ?>
</div>

</body>
</html>
