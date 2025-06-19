<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['dealer_id'])) {
    header("Location: dealer_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Submission ID is missing.";
    exit();
}

$trade_id = $_GET['id'];

// Fetch trade & vehicle details
$stmt = $pdo->prepare("
    SELECT t.*, u.name AS user_name, u.email, u.phone,
           v.make, v.model, v.year, v.mileage, v.condition, v.image
    FROM trades t
    JOIN users u ON t.user_id = u.id
    JOIN vehicles v ON t.vehicle_id = v.id
    WHERE t.id = ?
");
$stmt->execute([$trade_id]);
$submission = $stmt->fetch();

if (!$submission) {
    echo "Submission not found.";
    exit();
}

// Handle offer update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quoted_price = $_POST['quoted_price'];
    $status = $_POST['status'];

    $update = $pdo->prepare("UPDATE trades SET quoted_price = ?, status = ? WHERE id = ?");
    if ($update->execute([$quoted_price, $status, $trade_id])) {
        echo "<script>alert('Offer updated successfully.'); window.location.href='dealer_submissions.php';</script>";
        exit();
    } else {
        echo "<p>Failed to update offer.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Submission Detail</title>
  <style>
    body {
      font-family: Arial;
      background: #f4f4f4;
      margin: 0; padding: 0;
    }

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
      font-size: 1.4rem;
      text-align: center;
      margin-bottom: 1rem;
    }

    nav ul {
      list-style: none;
      padding: 0;
    }

    nav ul li {
      margin: 15px 0;
    }

    nav ul li a {
      color: #fff;
      text-decoration: none;
    }

    nav ul li a:hover {
      color: #FFB823;
    }

    .container {
      margin-left: 250px;
      padding: 30px;
    }

    .vehicle-card {
      background: #fff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      display: flex;
    }

    .vehicle-card img {
      max-width: 300px;
      margin-right: 30px;
      border-radius: 6px;
    }

    .details {
      flex: 1;
    }

    .details h3 {
      margin-top: 0;
      color: #2D4F2B;
    }

    .details p {
      margin: 6px 0;
    }

    .form-section {
      background: #fff;
      margin-top: 30px;
      padding: 20px;
      border-left: 5px solid #FFB823;
    }

    label {
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 15px;
    }

    .btn {
      background: #708A58;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn:hover {
      background: #2D4F2B;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
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
  <h2>Submission Detail</h2>

  <div class="vehicle-card">
    <img src="../uploads/<?= htmlspecialchars($submission['image']) ?>" alt="Vehicle Image">
    <div class="details">
      <h3><?= htmlspecialchars($submission['make']) ?> <?= htmlspecialchars($submission['model']) ?> (<?= $submission['year'] ?>)</h3>
      <p><strong>Mileage:</strong> <?= number_format($submission['mileage']) ?> km</p>
      <p><strong>Condition:</strong> <?= htmlspecialchars($submission['condition']) ?></p>
      <p><strong>Submitted by:</strong> <?= htmlspecialchars($submission['user_name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($submission['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($submission['phone']) ?></p>
      <p><strong>Submitted on:</strong> <?= htmlspecialchars($submission['submission_date']) ?></p>
    </div>
  </div>

  <div class="form-section">
    <h3>Submit / Revise Offer</h3>
    <form method="POST">
      <label>Quoted Price (KES)</label>
      <input type="number" name="quoted_price" value="<?= $submission['quoted_price'] ?? '' ?>" required>

      <label>Status</label>
      <select name="status" required>
        <option value="Pending" <?= $submission['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Approved" <?= $submission['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
        <option value="Rejected" <?= $submission['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>

      <button class="btn" type="submit">Submit Offer</button>
    </form>
  </div>
</div>

</body>
</html>
