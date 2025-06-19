<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all dealers
$stmt = $pdo->query("SELECT * FROM dealers ORDER BY id DESC");
$dealers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Dealers - Admin Panel</title>
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

    h1, h2 {
      color: #2D4F2B;
      margin-top: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      margin-top: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #FFB823;
      color: #2D4F2B;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .btn {
      background-color: #2D4F2B;
      color: #fff;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      text-decoration: none;
      font-size: 14px;
    }

    .btn:hover {
      background-color: #708A58;
    }

    .doc-links a {
      margin-right: 10px;
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
  <h1>Registered Dealers</h1>

  <?php if (count($dealers) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Company</th>
          <th>Contact Person</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Location</th>
          <th>Documents</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dealers as $d): ?>
        <tr>
          <td><?= $d['id'] ?></td>
          <td><?= htmlspecialchars($d['company_name']) ?></td>
          <td><?= htmlspecialchars($d['contact_person']) ?></td>
          <td><?= htmlspecialchars($d['email']) ?></td>
          <td><?= htmlspecialchars($d['phone']) ?></td>
          <td><?= htmlspecialchars($d['location']) ?></td>
          <td class="doc-links">
            <?php if ($d['pin_certificate']): ?>
              <a href="../uploads/<?= htmlspecialchars($d['pin_certificate']) ?>" class="btn" target="_blank">PIN</a>
            <?php endif; ?>
            <?php if ($d['license_document']): ?>
              <a href="../uploads/<?= htmlspecialchars($d['license_document']) ?>" class="btn" target="_blank">License</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No dealers found.</p>
  <?php endif; ?>
</div>

</body>
</html>
