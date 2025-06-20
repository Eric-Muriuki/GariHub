<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT trades.*, 
           vehicles.make, vehicles.model, vehicles.year, vehicles.image,
           users.name AS user_name
    FROM trades
    JOIN vehicles ON trades.vehicle_id = vehicles.id
    JOIN users ON trades.user_id = users.id
    ORDER BY trades.submission_date DESC
");
$stmt->execute();
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Trade Submissions</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f5f5f5;
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

    h1 {
      color: #2D4F2B;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      margin-top: 10px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #FFB823;
      color: #2D4F2B;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    .vehicle-img {
      width: 80px;
      height: auto;
      border-radius: 5px;
    }

    .btn {
      background: #2D4F2B;
      color: white;
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 4px;
      font-size: 14px;
    }

    .btn:hover {
      background: #708A58;
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
  <h1>All User Trade-ins</h1>

  <?php if (count($submissions) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Vehicle</th>
          <th>Owner</th>
          <th>Submitted</th>
          <th>Status</th>
          <th>Quote (Ksh)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($submissions as $s): ?>
        <tr>
          <td>
            <img src="../uploads/<?= htmlspecialchars($s['image']) ?>" class="vehicle-img" alt="vehicle"><br>
            <?= htmlspecialchars($s['make']) . ' ' . htmlspecialchars($s['model']) ?> (<?= $s['year'] ?>)
          </td>
          <td><?= htmlspecialchars($s['user_name']) ?></td>
          <td><?= htmlspecialchars($s['submission_date']) ?></td>
          <td><?= htmlspecialchars($s['status']) ?></td>
          <td><?= number_format($s['quoted_price'], 2) ?></td>
          <td><a href="admin_view_submission.php?id=<?= $s['id'] ?>" class="btn">View</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No submissions found.</p>
  <?php endif; ?>
</div>

</body>
</html>
