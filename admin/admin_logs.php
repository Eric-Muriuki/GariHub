<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM logs ORDER BY id DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Logs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f4f4f4;
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

    h2 {
      color: #2D4F2B;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #708A58;
      color: white;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .actor {
      font-weight: bold;
      color: #FFB823;
    }

    @media (max-width: 768px) {
      .main {
        margin-left: 0;
        padding: 15px;
      }

      table, thead, tbody, th, td, tr {
        display: block;
      }

      thead {
        display: none;
      }

      tr {
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        background: white;
      }

      td {
        padding: 10px;
        border: none;
        position: relative;
      }

      td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #2D4F2B;
        display: block;
        margin-bottom: 5px;
      }
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
  <h2>System Activity Logs</h2>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Actor</th>
        <th>Action</th>
        <th>Context</th>
        <th>Timestamp</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($logs as $i => $log): ?>
        <tr>
          <td data-label="#"> <?= $i + 1 ?> </td>
          <td data-label="Actor" class="actor"><?= ucfirst($log['actor_type']) ?> ID: <?= $log['actor_id'] ?></td>
          <td data-label="Action"><?= htmlspecialchars($log['action']) ?></td>
          <td data-label="Context"><?= htmlspecialchars($log['context']) ?></td>
          <td data-label="Timestamp"><?= htmlspecialchars($log['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
