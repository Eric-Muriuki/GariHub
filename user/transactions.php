<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch transactions for the user
$stmt = $pdo->prepare("
    SELECT 
        t.id, t.amount, t.date, t.proof,
        v.make, v.model, v.image,
        tr.status AS trade_status
    FROM transactions t
    JOIN vehicles v ON t.vehicle_id = v.id
    LEFT JOIN trades tr ON tr.vehicle_id = v.id AND tr.user_id = t.user_id
    WHERE t.user_id = ?
    ORDER BY t.date DESC
");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Transactions | GariHub</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f5f5f5;
    }

    .main-content {
      margin-left: 240px;
      padding: 30px;
    }

    h2 {
      color: #2D4F2B;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    th {
      background: #FFB823;
      color: #2D4F2B;
    }

    .vehicle-thumb {
      width: 80px;
      border-radius: 4px;
    }

    .btn {
      padding: 6px 12px;
      border: none;
      background: #708A58;
      color: white;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid #708A58;
      color: #708A58;
    }

    input[type="file"] {
      margin-top: 5px;
    }

    .muted {
      color: gray;
      font-style: italic;
    }

    .form-inline {
      display: flex;
      flex-direction: column;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      table, thead, tbody, th, td, tr {
        display: block;
      }

      td {
        margin-bottom: 10px;
      }
    }
  </style>
</head>
<body>

<div class="main-content">
  <h2>My Transactions</h2>

  <?php if (count($transactions) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Vehicle</th>
          <th>Amount (KES)</th>
          <th>Date</th>
          <th>Proof</th>
          <th>Status</th>
          <th>Release Doc</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($transactions as $t): ?>
          <tr>
            <td>
              <img src="../uploads/<?= htmlspecialchars($t['image']) ?>" alt="Vehicle" class="vehicle-thumb">
              <br><?= htmlspecialchars($t['make']) . ' ' . htmlspecialchars($t['model']) ?>
            </td>
            <td><?= number_format($t['amount'], 2) ?></td>
            <td><?= htmlspecialchars($t['date']) ?></td>
            <td>
              <?php if ($t['proof']): ?>
                <a href="../uploads/proofs/<?= htmlspecialchars($t['proof']) ?>" target="_blank" class="btn btn-sm">Download</a>
              <?php else: ?>
                <form action="upload_proof.php" method="post" enctype="multipart/form-data" class="form-inline">
                  <input type="hidden" name="transaction_id" value="<?= $t['id'] ?>">
                  <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required>
                  <button type="submit" class="btn btn-sm">Upload</button>
                </form>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($t['trade_status'] ?? 'Pending') ?></td>
            <td>
              <?php
                $releaseDoc = "release_doc_{$t['id']}.pdf";
                if (file_exists("../uploads/releases/$releaseDoc")):
              ?>
                <a href="../uploads/releases/<?= $releaseDoc ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
              <?php else: ?>
                <span class="muted">Pending</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>You have no transactions yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
