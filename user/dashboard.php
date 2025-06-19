<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$userStmt = $pdo->prepare("SELECT name, email, phone, kyc_document FROM users WHERE id = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch();

// Trade-ins count
$tradeStmt = $pdo->prepare("SELECT COUNT(*) FROM trades WHERE user_id = ?");
$tradeStmt->execute([$user_id]);
$tradeCount = $tradeStmt->fetchColumn();

// Transactions count
$txnStmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ?");
$txnStmt->execute([$user_id]);
$txnCount = $txnStmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f9f9f9;
    }

    .dashboard-container {
      margin-left: 240px;
      padding: 30px;
    }

    h2 {
      color: #2D4F2B;
      margin-bottom: 20px;
    }

    .cards {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      width: 250px;
      flex: 1;
      transition: transform 0.2s ease;
    }

    .card:hover {
      transform: translateY(-3px);
    }

    .card h3 {
      margin: 0 0 10px;
      font-size: 18px;
      color: #708A58;
    }

    .card p {
      font-size: 22px;
      font-weight: bold;
      color: #2D4F2B;
    }

    .profile-box {
      background: white;
      margin-top: 30px;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .profile-box h3 {
      color: #2D4F2B;
    }

    .profile-box p {
      margin: 10px 0;
    }

    .kyc-status {
      margin-top: 10px;
      font-weight: bold;
      color: #FFB823;
    }

    .kyc-status.verified {
      color: green;
    }
  </style>
</head>
<body>

<div class="dashboard-container">
  <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>

  <div class="cards">
    <div class="card">
      <h3>My Trade-ins</h3>
      <p><?= $tradeCount ?></p>
    </div>

    <div class="card">
      <h3>Transactions</h3>
      <p><?= $txnCount ?></p>
    </div>

    <div class="card">
      <h3>KYC Status</h3>
      <p class="kyc-status <?= $user['kyc_document'] ? 'verified' : '' ?>">
        <?= $user['kyc_document'] ? 'Verified ✅' : 'Not Uploaded ⚠️' ?>
      </p>
    </div>
  </div>

  <div class="profile-box">
    <h3>Profile Details</h3>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
    <p><a href="profile.php" class="btn">Edit Profile</a></p>
  </div>
</div>

</body>
</html>
