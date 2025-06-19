<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: ../dealer_login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

// Fetch logs for this dealer
$stmt = $pdo->prepare("SELECT * FROM logs WHERE actor_type = 'dealer' AND actor_id = ? ORDER BY id DESC");
$stmt->execute([$dealer_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dealer Logs - GariHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f9f9f9;
    }

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
      color: #FFF1CA;
      text-align: center;
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

    .main {
      margin-left: 240px;
      padding: 30px;
    }

    h1 {
      color: #2D4F2B;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
      border-radius: 8px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
    }

    th {
      background: #2D4F2B;
      color: white;
    }

    tr:nth-child(even) {
      background: #f0f0f0;
    }

    .muted {
      color: #777;
      font-size: 0.9rem;
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

<!-- Main Content -->
<div class="main">
  <h1>Activity Logs</h1>

  <?php if (count($logs) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Action</th>
          <th>Context</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
          <td><?= htmlspecialchars($log['action']) ?></td>
          <td><?= htmlspecialchars($log['context']) ?></td>
          <td class="muted"><?= date('Y-m-d H:i', strtotime($log['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No activity logged yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
