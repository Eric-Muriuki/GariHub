<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Count total entries
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalDealers = $pdo->query("SELECT COUNT(*) FROM dealers")->fetchColumn();
$totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$totalTrades = $pdo->query("SELECT COUNT(*) FROM trades")->fetchColumn();
$totalTransactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Reports</title>
  <style>
    body {
      font-family: Arial;
      padding: 20px;
      background-color: #f8f9fa;
    }
    h2 {
      color: #2D4F2B;
    }
    .report-card {
      background-color: white;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .report-card h3 {
      margin-top: 0;
      color: #708A58;
    }
    .btn {
      background-color: #FFB823;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
    }
  </style>
</head>
<body>

<h2>Summary Reports</h2>

<div class="report-card">
  <h3>System Overview</h3>
  <p>Total Users: <?= $totalUsers ?></p>
  <p>Total Dealers: <?= $totalDealers ?></p>
  <p>Total Vehicles: <?= $totalVehicles ?></p>
  <p>Total Trade Submissions: <?= $totalTrades ?></p>
  <p>Total Transactions: <?= $totalTransactions ?></p>
</div>

<a href="generate_report_pdf.php" class="btn">Download PDF Report</a>

</body>
</html>
