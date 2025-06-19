<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Trade ID missing.";
    exit();
}

$trade_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch trade & vehicle info
$stmt = $pdo->prepare("
    SELECT t.*, 
           v.make, v.model, v.year, v.price, v.mileage, v.condition, v.image
    FROM trades t
    JOIN vehicles v ON t.vehicle_id = v.id
    WHERE t.id = ? AND t.user_id = ?
");
$stmt->execute([$trade_id, $user_id]);
$trade = $stmt->fetch();

if (!$trade) {
    echo "Trade not found or unauthorized.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trade Detail | GariHub</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0; padding: 0;
      background-color: #f6f6f6;
    }

    .main-content {
      margin-left: 240px;
      padding: 30px;
    }

    .trade-card {
      background: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      max-width: 800px;
    }

    .trade-card img {
      width: 100%;
      max-width: 300px;
      border-radius: 8px;
    }

    .trade-info {
      margin-top: 20px;
    }

    .trade-info p {
      margin: 8px 0;
    }

    .highlight {
      color: #2D4F2B;
      font-weight: bold;
    }

    .status-box {
      display: inline-block;
      padding: 6px 10px;
      background-color: #708A58;
      color: white;
      border-radius: 4px;
      font-size: 14px;
    }

    .btn {
      padding: 8px 16px;
      background-color: #FFB823;
      color: #2D4F2B;
      text-decoration: none;
      border-radius: 4px;
      display: inline-block;
      margin-top: 15px;
    }

    .doc-section {
      margin-top: 30px;
    }

    .doc-section h3 {
      color: #2D4F2B;
    }

    .doc-link {
      display: inline-block;
      margin-right: 10px;
      background: #eee;
      padding: 5px 10px;
      border-radius: 4px;
      color: #2D4F2B;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="main-content">
  <h2>Trade Details</h2>

  <div class="trade-card">
    <h3><?= htmlspecialchars($trade['make']) . ' ' . htmlspecialchars($trade['model']) ?> (<?= $trade['year'] ?>)</h3>
    <img src="../uploads/<?= htmlspecialchars($trade['image']) ?>" alt="Vehicle Image">

    <div class="trade-info">
      <p><span class="highlight">Submitted:</span> <?= $trade['submission_date'] ?></p>
      <p><span class="highlight">Condition:</span> <?= $trade['condition'] ?></p>
      <p><span class="highlight">Mileage:</span> <?= number_format($trade['mileage']) ?> km</p>
      <p><span class="highlight">Quoted Price:</span> KES <?= number_format($trade['quoted_price']) ?></p>
      <p><span class="highlight">Status:</span> <span class="status-box"><?= htmlspecialchars($trade['status']) ?></span></p>
    </div>

    <div class="doc-section">
      <h3>Documents</h3>

      <?php
      $proofPath = "../uploads/proofs/proof_{$trade['id']}.pdf";
      $releasePath = "../uploads/releases/release_doc_{$trade['id']}.pdf";
      ?>

      <?php if (file_exists($proofPath)): ?>
        <a class="doc-link" href="<?= $proofPath ?>" target="_blank">Download Payment Proof</a>
      <?php else: ?>
        <p>No payment proof uploaded.</p>
      <?php endif; ?>

      <?php if (file_exists($releasePath)): ?>
        <a class="doc-link" href="<?= $releasePath ?>" target="_blank">Download Release Document</a>
      <?php else: ?>
        <p>No release document yet.</p>
      <?php endif; ?>
    </div>

    <a href="trade.php" class="btn">← Back to My Trades</a>
  </div>
</div>

</body>
</html>
