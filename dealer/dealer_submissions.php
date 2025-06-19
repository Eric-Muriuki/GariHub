<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: dealer_login.php");
    exit();
}

$dealer_id = $_SESSION['dealer_id'];

$stmt = $pdo->prepare("
    SELECT t.*, u.name AS user_name, u.phone, u.email, v.make, v.model, v.year, v.image
    FROM trades t
    JOIN vehicles v ON t.vehicle_id = v.id
    JOIN users u ON t.user_id = u.id
    WHERE v.dealer_id = ?
    ORDER BY t.submission_date DESC
");
$stmt->execute([$dealer_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dealer Submissions - GariHub</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0; padding: 0;
      background: #f9f9f9;
    }

    .container {
      padding: 30px;
      margin-left: 250px;
    }

    h2 {
      color: #2D4F2B;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #FFB823;
      color: #2D4F2B;
    }

    tr:hover {
      background-color: #f2f2f2;
    }

    img.vehicle-thumb {
      width: 80px;
      border-radius: 4px;
    }

    .btn {
      padding: 6px 12px;
      background: #708A58;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
    }

    .btn:hover {
      background: #2D4F2B;
    }

    .user-info {
      font-size: 0.9em;
      color: #333;
    }

    .status {
      font-weight: bold;
      color: #2D4F2B;
    }

    .no-data {
      padding: 20px;
      text-align: center;
      background: #fff;
      border: 1px solid #ccc;
      margin-top: 20px;
    }

    /* Sidebar quick style */
    nav {
      width: 220px;
      position: fixed;
      top: 0; left: 0;
      height: 100%;
      background: #2D4F2B;
      color: #FFF1CA;
      padding: 1rem;
    }

    nav h2 {
      font-size: 1.5rem;
      text-align: center;
    }

    nav ul {
      list-style: none;
      padding: 0;
    }

    nav ul li {
      margin: 20px 0;
    }

    nav ul li a {
      color: #fff;
      text-decoration: none;
    }

    nav ul li a:hover {
      color: #FFB823;
    }
  </style>
</head>
<body>

<!-- Dealer Sidebar -->
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

<div class="container">
  <h2>Vehicle Submissions</h2>

  <?php if (count($submissions) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Vehicle</th>
          <th>Owner</th>
          <th>Quoted Price</th>
          <th>Status</th>
          <th>Submitted</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($submissions as $s): ?>
        <tr>
          <td>
            <img src="../uploads/<?= htmlspecialchars($s['image']) ?>" class="vehicle-thumb" alt="Car"><br>
            <?= htmlspecialchars($s['make']) . ' ' . htmlspecialchars($s['model']) ?> (<?= $s['year'] ?>)
          </td>
          <td class="user-info">
            <?= htmlspecialchars($s['user_name']) ?><br>
            <?= htmlspecialchars($s['email']) ?><br>
            <?= htmlspecialchars($s['phone']) ?>
          </td>
          <td>KES <?= number_format($s['quoted_price']) ?></td>
          <td class="status"><?= htmlspecialchars($s['status']) ?></td>
          <td><?= htmlspecialchars($s['submission_date']) ?></td>
          <td>
            <a class="btn" href="submission_detail.php?id=<?= $s['id'] ?>">View</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="no-data">No vehicle submissions yet.</div>
  <?php endif; ?>
</div>

<script>
  // Placeholder for possible interactivity
</script>
</body>
</html>
