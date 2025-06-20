<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../dealer_login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch metrics
$submissions = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE dealer_id = ?");
$submissions->execute([$dealer_id]);
$total_submissions = $submissions->fetchColumn();

$offers = $pdo->prepare("SELECT COUNT(*) FROM trades WHERE vehicle_id IN (SELECT id FROM vehicles WHERE dealer_id = ?)");
$offers->execute([$dealer_id]);
$total_offers = $offers->fetchColumn();

$transactions = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE vehicle_id IN (SELECT id FROM vehicles WHERE dealer_id = ?)");
$transactions->execute([$dealer_id]);
$total_transactions = $transactions->fetchColumn();

$support = $pdo->prepare("SELECT COUNT(*) FROM support WHERE id = ? AND status = 'Open'");
$support->execute([$dealer_id]);
$open_tickets = $support->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dealer Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
    }

    /* Sidebar */
    .sidebar {
      width: 220px;
      height: 100vh;
      background: #2D4F2B;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px;
      color: white;
    }

    .sidebar h2 {
      text-align: center;
      color: #FFF1CA;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin-top: 30px;
    }

    .sidebar li {
      margin: 15px 0;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      font-weight: 500;
    }

    .sidebar a:hover {
      color: #FFB823;
    }

    /* Main content */
    .main {
      margin-left: 240px;
      padding: 30px;
    }

    .dashboard-header {
      margin-bottom: 20px;
    }

    .dashboard-header h1 {
      color: #2D4F2B;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
      text-align: center;
      transition: 0.3s ease;
    }

    .card:hover {
      transform: scale(1.03);
    }

    .card h3 {
      margin: 0;
      color: #2D4F2B;
    }

    .card p {
      font-size: 2rem;
      margin: 10px 0;
      color: #FFB823;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
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
</div>

<!-- Main Dashboard -->
<div class="main">
  <div class="dashboard-header">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['dealer_name'] ?? 'Dealer') ?></h1>
    <p>Here's a quick summary of your account</p>
  </div>

  <div class="cards">
    <div class="card">
      <h3>Vehicle Submissions</h3>
      <p><?= $total_submissions ?></p>
    </div>

    <div class="card">
      <h3>Offers Made</h3>
      <p><?= $total_offers ?></p>
    </div>

    <div class="card">
      <h3>Transactions</h3>
      <p><?= $total_transactions ?></p>
    </div>

    <div class="card">
      <h3>Open Tickets</h3>
      <p><?= $open_tickets ?></p>
    </div>
  </div>
</div>

</body>
</html>
