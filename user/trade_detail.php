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

// Fetch trade and vehicle info
$stmt = $pdo->prepare("
    SELECT t.*, v.make, v.model, v.year, v.price, v.mileage, v.condition, v.image
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

// Handle offer response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['offer_id']) && isset($_POST['response'])) {
    $offer_id = $_POST['offer_id'];
    $response = $_POST['response'];

    if (in_array($response, ['Accepted', 'Rejected'])) {
        $pdo->prepare("UPDATE offers SET status = ? WHERE id = ?")->execute([$response, $offer_id]);
    }
}

// Fetch offers from dealers
$offers = $pdo->prepare("
    SELECT o.*, d.company_name AS dealer_name
    FROM offers o
    JOIN dealers d ON o.dealer_id = d.id
    WHERE o.trade_id = ?
    ORDER BY o.created_at DESC
");
$offers->execute([$trade_id]);
$dealer_offers = $offers->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trade Detail | GariHub</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f6f6f6; margin: 0; padding: 0; }
    .main-content { margin-left: 240px; padding: 30px; }
    .trade-card, .offer-card {
      background: white; padding: 25px; border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 30px;
    }
    .trade-card img { width: 100%; max-width: 300px; border-radius: 8px; }
    .highlight { color: #2D4F2B; font-weight: bold; }
    .status-box { padding: 6px 10px; background: #708A58; color: white; border-radius: 4px; font-size: 14px; }
    .btn { padding: 8px 16px; background: #FFB823; color: #2D4F2B; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 15px; }
    .doc-link { display: inline-block; background: #eee; padding: 5px 10px; border-radius: 4px; color: #2D4F2B; text-decoration: none; }
    .offer-section h3 { color: #2D4F2B; }
    form.offer-response { margin-top: 10px; }
    form.offer-response button {
      margin-right: 10px; padding: 6px 12px; border: none; cursor: pointer; border-radius: 4px;
    }
    .accept-btn { background: #2D4F2B; color: white; }
    .reject-btn { background: #B22222; color: white; }
  </style>
</head>
<body>

<div class="main-content">
  <h2>Trade Details</h2>

  <div class="trade-card">
    <h3><?= htmlspecialchars($trade['make']) . ' ' . htmlspecialchars($trade['model']) ?> (<?= $trade['year'] ?>)</h3>
    <img src="../uploads/<?= htmlspecialchars($trade['image']) ?>" alt="Vehicle Image">
    <p><span class="highlight">Condition:</span> <?= htmlspecialchars($trade['condition']) ?></p>
    <p><span class="highlight">Mileage:</span> <?= number_format($trade['mileage']) ?> km</p>
    <p><span class="highlight">Quoted Price:</span> KES <?= number_format($trade['quoted_price']) ?></p>
    <p><span class="highlight">Status:</span> <span class="status-box"><?= htmlspecialchars($trade['status']) ?></span></p>
    <a href="trade.php" class="btn">← Back to My Trades</a>
  </div>

  <div class="offer-section">
    <h3>Dealer Offers</h3>

    <?php if (count($dealer_offers) === 0): ?>
      <p>No offers received yet.</p>
    <?php else: ?>
      <?php foreach ($dealer_offers as $offer): ?>
        <div class="offer-card">
          <p><strong>Dealer:</strong> <?= htmlspecialchars($offer['dealer_name']) ?></p>
          <p><strong>Offer Price:</strong> KES <?= number_format($offer['offer_price']) ?></p>
          <p><strong>Message:</strong> <?= htmlspecialchars($offer['offer_notes']) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($offer['status']) ?></p>

          <?php if ($offer['status'] === 'Pending'): ?>
          <form method="POST" class="offer-response">
            <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
            <button type="submit" name="response" value="Accepted" class="accept-btn">Accept</button>
            <button type="submit" name="response" value="Rejected" class="reject-btn">Reject</button>
          </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
