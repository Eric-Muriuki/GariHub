<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user logs
$stmt = $pdo->prepare("SELECT * FROM logs WHERE actor_type = 'user' AND actor_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Activity Logs</title>
  <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f5f5f5;
        margin: 0;
    }
    .main-content {
        margin-left: 240px;
        padding: 30px;
    }
    h2 {
        color: #2D4F2B;
    }
    .log-table {
        width: 100%;
        background: white;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .log-table th, .log-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }
    .log-table th {
        background-color: #2D4F2B;
        color: white;
    }
    .log-table tr:hover {
        background-color: #f9f9f9;
    }
    .muted {
        color: #888;
        font-style: italic;
    }
  </style>
</head>
<body>

<div class="main-content">
  <h2>My Activity Logs</h2>

  <?php if (count($logs) > 0): ?>
    <table class="log-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Action</th>
          <th>Details</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $index => $log): ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['context']) ?></td>
            <td><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="muted">No logs found for your account.</p>
  <?php endif; ?>
</div>

</body>
</html>
