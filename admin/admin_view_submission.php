<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Trade ID not specified.";
    exit();
}

$trade_id = $_GET['id'];

// Fetch trade submission details
$stmt = $pdo->prepare("
    SELECT trades.*, 
           vehicles.make, vehicles.model, vehicles.year, vehicles.price, vehicles.mileage, vehicles.condition, vehicles.image,
           users.name AS user_name, users.email, users.phone
    FROM trades
    JOIN vehicles ON trades.vehicle_id = vehicles.id
    JOIN users ON trades.user_id = users.id
    WHERE trades.id = ?
");
$stmt->execute([$trade_id]);
$trade = $stmt->fetch();

if (!$trade) {
    echo "Trade submission not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - View Submission</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f8f8;
      margin: 0; padding: 0;
    }

    .header {
      background-color: #2D4F2B;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .container {
      max-width: 900px;
      margin: 30px auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .vehicle-img {
      width: 200px;
      height: auto;
      border-radius: 6px;
    }

    h2 {
      color: #2D4F2B;
    }

    .section {
      margin-bottom: 25px;
    }

    label {
      font-weight: bold;
    }

    .btn {
      background-color: #FFB823;
      color: #2D4F2B;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #e5a600;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    .back-link {
      margin-top: 20px;
      display: inline-block;
    }
  </style>
</head>
<body>

<div class="header">
  <h1>Admin Panel - View Submission</h1>
</div>

<div class="container">
  <h2>Trade-in Details</h2>

  <div class="section">
    <img src="../uploads/<?= htmlspecialchars($trade['image']) ?>" alt="Vehicle" class="vehicle-img">
  </div>

  <div class="section">
    <table>
      <tr>
        <td><label>Vehicle:</label></td>
        <td><?= htmlspecialchars($trade['make']) ?> <?= htmlspecialchars($trade['model']) ?> (<?= $trade['year'] ?>)</td>
      </tr>
      <tr>
        <td><label>Price:</label></td>
        <td>KES <?= number_format($trade['price'], 2) ?></td>
      </tr>
      <tr>
        <td><label>Mileage:</label></td>
        <td><?= number_format($trade['mileage']) ?> KM</td>
      </tr>
      <tr>
        <td><label>Condition:</label></td>
        <td><?= htmlspecialchars($trade['condition']) ?></td>
      </tr>
      <tr>
        <td><label>Quoted Price:</label></td>
        <td><strong>KES <?= number_format($trade['quoted_price']) ?></strong></td>
      </tr>
      <tr>
        <td><label>Status:</label></td>
        <td><?= htmlspecialchars($trade['status']) ?></td>
      </tr>
      <tr>
        <td><label>Submission Date:</label></td>
        <td><?= htmlspecialchars($trade['submission_date']) ?></td>
      </tr>
    </table>
  </div>

  <h3>User Information</h3>
  <div class="section">
    <table>
      <tr>
        <td><label>Name:</label></td>
        <td><?= htmlspecialchars($trade['user_name']) ?></td>
      </tr>
      <tr>
        <td><label>Email:</label></td>
        <td><?= htmlspecialchars($trade['email']) ?></td>
      </tr>
      <tr>
        <td><label>Phone:</label></td>
        <td><?= htmlspecialchars($trade['phone']) ?></td>
      </tr>
    </table>
  </div>

  <a href="admin_submissions.php" class="btn back-link">← Back to Submissions</a>
</div>

</body>
</html>
