<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/user_sidebar.php';
$user_id = $_SESSION['user_id'];

// Fetch approved trades with related vehicle info and proof documents
$stmt = $pdo->prepare("
    SELECT 
        t.id AS trade_id, t.quoted_price, t.submission_date, t.status,
        v.make, v.model, v.year, v.image,
        r.file_path AS release_file,
        p.file_path AS payment_file
    FROM trades t
    JOIN vehicles v ON t.vehicle_id = v.id
    LEFT JOIN releases r ON t.id = r.trade_id
    LEFT JOIN payments p ON t.id = p.trade_id
    WHERE t.user_id = ? AND t.status = 'Approved'
    ORDER BY t.submission_date DESC
");
$stmt->execute([$user_id]);
$trades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approved Trades | GariHub</title>
  <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }

    .main-content {
        margin-left: 240px;
        padding: 30px;
    }

    h2 {
        color: #2D4F2B;
        margin-bottom: 20px;
    }

    .trade-card {
        background: #fff;
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .trade-card img {
        width: 150px;
        border-radius: 6px;
        margin-right: 20px;
    }

    .trade-details {
        flex: 1;
    }

    .trade-details h4 {
        margin: 0;
        color: #2D4F2B;
    }

    .btn {
        padding: 6px 12px;
        margin-top: 10px;
        margin-right: 10px;
        background: #708A58;
        color: white;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn:hover {
        background: #2D4F2B;
    }

    .btn.disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .no-trades {
        background: #FFF1CA;
        padding: 20px;
        border-left: 5px solid #FFB823;
        color: #2D4F2B;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 15px;
        }
    }
  </style>
</head>
<body>

<div class="main-content">
    <h2>Approved Trade-ins</h2>

    <?php if (count($trades) === 0): ?>
        <div class="no-trades">You have no approved trades yet.</div>
    <?php else: ?>
        <?php foreach ($trades as $trade): ?>
            <div class="trade-card">
                <img src="../uploads/<?= htmlspecialchars($trade['image']) ?>" alt="Vehicle Image">
                <div class="trade-details">
                    <h4><?= htmlspecialchars($trade['make']) ?> <?= htmlspecialchars($trade['model']) ?> (<?= $trade['year'] ?>)</h4>
                    <p><strong>Quoted Price:</strong> KES <?= number_format($trade['quoted_price']) ?></p>
                    <p><strong>Submitted On:</strong> <?= htmlspecialchars($trade['submission_date']) ?></p>

                    <a class="btn <?= $trade['release_file'] ? '' : 'disabled' ?>" 
                       href="<?= $trade['release_file'] ? '../uploads/' . htmlspecialchars($trade['release_file']) : '#' ?>" 
                       target="_blank">
                        View Release Proof
                    </a>

                    <a class="btn <?= $trade['payment_file'] ? '' : 'disabled' ?>" 
                       href="<?= $trade['payment_file'] ? '../uploads/' . htmlspecialchars($trade['payment_file']) : '#' ?>" 
                       target="_blank">
                        View Payment Proof
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
