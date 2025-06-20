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
$offers = $stmt->fetchAll();

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment'])) {
    $trade_id = $_POST['trade_id'];
    $file = $_FILES['payment']['name'];
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!in_array(strtolower($ext), $allowed)) {
        $message = "❌ Invalid file type. Only PDF, JPG, JPEG, PNG allowed.";
    } else {
        $targetDir = "../uploads/payments/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filePath = $targetDir . "payment_proof_" . $trade_id . "." . $ext;

        if (move_uploaded_file($_FILES['payment']['tmp_name'], $filePath)) {
            // Log action
            $log = $pdo->prepare("INSERT INTO logs (actor_type, actor_id, action, context) VALUES ('dealer', ?, 'Uploaded Payment Proof', ?)");
            $context = "Uploaded payment proof for Trade ID: $trade_id";
            $log->execute([$dealer_id, $context]);

            $message = "✅ Payment proof uploaded successfully.";
        } else {
            $message = "❌ Failed to upload payment proof.";
        }

        $selected_trade_id = $trade_id;
    }
}

// Check if payment file exists
$paymentFile = null;
if ($selected_trade_id) {
    foreach (['pdf', 'jpg', 'jpeg', 'png'] as $ext) {
        $testPath = "../uploads/payments/payment_proof_" . $selected_trade_id . "." . $ext;
        if (file_exists($testPath)) {
            $paymentFile = $testPath;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upload Payment Proof</title>
  <style>
    body {
      font-family: Arial;
      background: #f9f9f9;
      margin: 0;
    }

    nav {
      width: 220px;
      height: 100vh;
      background-color: #2D4F2B;
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px;
    }

    nav h2 {
      color: #FFF1CA;
      text-align: center;
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

    .form-box {
      background: white;
      padding: 20px;
      max-width: 600px;
      border-radius: 8px;
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

    .viewer {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
    }

    iframe {
      width: 100%;
      height: 600px;
      border: none;
    }

    img {
      max-width: 100%;
      border-radius: 8px;
      margin-top: 20px;
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
  <h2>Upload / View Payment Proof</h2>

  <div class="form-box">
    <form method="POST" enctype="multipart/form-data">
      <label>Select Accepted Offer:</label>
      <select name="trade_id" onchange="this.form.submit()" required>
        <option value="">-- Choose Offer --</option>
        <?php foreach ($offers as $offer): ?>
          <option value="<?= $offer['trade_id'] ?>" <?= $selected_trade_id == $offer['trade_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($offer['make']) ?> <?= htmlspecialchars($offer['model']) ?> (<?= $offer['year'] ?>) - <?= htmlspecialchars($offer['buyer']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <?php if ($selected_trade_id): ?>
        <label>Upload Payment Proof (PDF/JPG/PNG):</label>
        <input type="file" name="payment" accept=".pdf,.jpg,.jpeg,.png">
        <button type="submit">Upload</button>
      <?php endif; ?>
    </form>

    <?php if (!empty($message)): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
  </div>

  <?php if ($paymentFile): ?>
    <div class="viewer">
      <h3>Viewing Uploaded Proof</h3>
      <?php if (str_ends_with($paymentFile, '.pdf')): ?>
        <iframe src="<?= $paymentFile ?>"></iframe>
      <?php else: ?>
        <img src="<?= $paymentFile ?>" alt="Payment Proof">
      <?php endif; ?>
    </div>
  <?php elseif ($selected_trade_id): ?>
    <p>No payment proof uploaded for this offer yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
