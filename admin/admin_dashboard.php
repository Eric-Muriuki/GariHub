<?php 
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_dealers = $pdo->query("SELECT COUNT(*) FROM dealers")->fetchColumn();
$total_vehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$total_trades = $pdo->query("SELECT COUNT(*) FROM trades")->fetchColumn();
$total_support = $pdo->query("SELECT COUNT(*) FROM support")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f4f4f4;
    }

    .admin-sidebar {
      width: 220px;
      height: 100vh;
      background: #2D4F2B;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px;
      color: white;
    }

    .admin-sidebar h2 {
      text-align: center;
      color: #FFF1CA;
      margin-bottom: 30px;
    }

    .admin-sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .admin-sidebar li {
      margin: 15px 0;
    }

    .admin-sidebar a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      display: block;
      padding: 8px;
      border-radius: 4px;
    }

    .admin-sidebar a:hover {
      background-color: #FFB823;
      color: #2D4F2B;
    }

    .main {
      margin-left: 240px;
      padding: 30px;
    }

    .dashboard-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .card {
      background-color: white;
      width: 200px;
      padding: 20px;
      text-align: center;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card h3 {
      margin: 0;
      font-size: 18px;
      color: #333;
    }

    .card p {
      font-size: 24px;
      color: #2D4F2B;
      font-weight: bold;
    }

    .logout {
      text-align: center;
      margin-top: 40px;
    }

    .logout a {
      text-decoration: none;
      color: #FF4D4D;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="admin-sidebar">
  <h2>🛠️ Admin Panel</h2>
  <ul>
    <li><a href="admin_dashboard.php">Dashboard</a></li>
    <li><a href="admin_dealers.php">Manage Dealers</a></li>
    <li><a href="admin_submissions.php">Vehicle Submissions</a></li>
    <li><a href="admin_logs.php">Activity Logs</a></li>
    <li><a href="admin_support.php">Support</a></li>
    <li><a href="admin_reports.php">Reports</a></li>
    <li><a href="../logout.php" style="color: #FFB823;">Logout</a></li>
  </ul>
</div>

<div class="main">
  <h1>Welcome, Admin</h1>
  <p>Here's a summary of platform activity:</p>

  <div class="dashboard-container">
    <div class="card">
      <h3>Total Users</h3>
      <p><?= $total_users ?></p>
    </div>
    <div class="card">
      <h3>Total Dealers</h3>
      <p><?= $total_dealers ?></p>
    </div>
    <div class="card">
      <h3>Vehicles</h3>
      <p><?= $total_vehicles ?></p>
    </div>
    <div class="card">
      <h3>Trade-Ins</h3>
      <p><?= $total_trades ?></p>
    </div>
    <div class="card">
      <h3>Support Tickets</h3>
      <p><?= $total_support ?></p>
    </div>
  </div>

  <div class="logout">
    <a href="../logout.php">🚪 Logout</a>
  </div>
</div>

</body>
</html>
