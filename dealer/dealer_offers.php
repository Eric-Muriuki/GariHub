<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: dealer_login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch offers submitted by this dealer
$stmt = $pdo->prepare("
    SELECT o.*, t.submission_date, v.make, v.model, v.year, v.image, u.name AS user_name
    FROM offers o
    JOIN trades t ON o.trade_id = t.id
    JOIN vehicles v ON t.vehicle_id = v.id
    JOIN users u ON t.user_id = u.id
    WHERE o.dealer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$dealer_id]);
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dealer Offers - GariHub</title>
  <style>
    body {
      font-family: Arial;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
    }

    nav {
      width: 220px;
      height: 100vh;
      position: fixed;
      background: #2D4F2B;
      color: #FFF1CA;
      padding: 1rem;
    }

    nav h2 {
      text-align: center;
      font-size: 1.4rem;
    }

    nav ul {
      list-style: none;
      padding: 0;
    }

    nav li {
      margin: 20px 0;
    }

    nav a {
      text-decoration: none;
      color: white;
    }

    nav a:hover {
      color: #FFB823;
    }

    .container {
      margin-left: 240px;
      padding: 30px;
    }

    h2 {
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      background: #fff;
      border-collapse: collapse;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #708A58;
      color: white;
    }

    .vehicle-thumb {
      width: 90px;
      border-radius: 4px;
    }

    .btn {
      padding: 6px 12px;
      background: #2D4F2B;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
    }

    .btn:hover {
      background: #FFB823;
      color: #2D4F2B;
    }

    .status-approved { color: green; font-weight: bold; }
    .status-rejected { color: red; font-weight: bold; }
    .status-pending { color: orange; font-weight: bold; }
  </style>
</head>
<body>

<!-- Sidebar -->
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

<!-- Main -->
<div class="container">
  <h2>Offers You've Made</h2>

  <?php if (count($offers) === 0): ?>
    <p>No offers have been submitted yet.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Vehicle</th>
          <th>Owner</th>
          <th>Offer Price</th>
          <th>Status</th>
          <th>Trade Submitted</th>
          <th>Your Offer Date</th>
         
        </tr>
      </thead>
      <tbody>
        <?php foreach ($offers as $offer): ?>
          <tr>
            <td>
              <img src="../uploads/<?= htmlspecialchars($offer['image']) ?>" class="vehicle-thumb" alt="Vehicle">
              <br><?= htmlspecialchars($offer['make']) ?> <?= htmlspecialchars($offer['model']) ?> (<?= $offer['year'] ?>)
            </td>
            <td><?= htmlspecialchars($offer['user_name']) ?></td>
            <td>KES <?= number_format($offer['offer_price']) ?></td>
            <td>
              <span class="status-<?= strtolower($offer['status']) ?>">
                <?= htmlspecialchars($offer['status']) ?>
              </span>
            </td>
            <td><?= htmlspecialchars($offer['submission_date']) ?></td>
            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($offer['created_at']))) ?></td>
            
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

</body>
</html>
