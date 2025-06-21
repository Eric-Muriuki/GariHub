<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/user_sidebar.php';

$user_id = $_SESSION['user_id'];
$trade_id = $_GET['id'] ?? null;

// Validate trade ownership
$stmt = $pdo->prepare("SELECT * FROM trades WHERE id = ? AND user_id = ?");
$stmt->execute([$trade_id, $user_id]);
$trade = $stmt->fetch();

if (!$trade) {
    echo "<p style='padding:20px;'>Invalid trade or access denied.</p>";
    exit();
}

// Handle offer acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_offer_id'])) {
    $offer_id = $_POST['accept_offer_id'];

    $stmt = $pdo->prepare("SELECT trade_id FROM offers WHERE id = ? AND trade_id = ? AND status = 'Pending'");
    $stmt->execute([$offer_id, $trade_id]);
    $offer = $stmt->fetch();

    if ($offer) {
        $pdo->prepare("UPDATE offers SET status = 'Accepted' WHERE id = ?")->execute([$offer_id]);
        $pdo->prepare("UPDATE offers SET status = 'Rejected' WHERE trade_id = ? AND id != ?")->execute([$trade_id, $offer_id]);
        $pdo->prepare("UPDATE trades SET status = 'Approved' WHERE id = ?")->execute([$trade_id]);
        $pdo->prepare("INSERT INTO logs (actor_type, actor_id, action, context) VALUES ('user', ?, 'Accepted Offer', ?)")
            ->execute([$user_id, "Accepted offer ID $offer_id for Trade ID $trade_id"]);

        header("Location: trade_detail.php?id=$trade_id&accepted=1");
        exit();
    }
}

// Handle offer rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_offer_id'])) {
    $offer_id = $_POST['reject_offer_id'];

    $stmt = $pdo->prepare("SELECT trade_id FROM offers WHERE id = ? AND trade_id = ? AND status = 'Pending'");
    $stmt->execute([$offer_id, $trade_id]);
    $offer = $stmt->fetch();

    if ($offer) {
        $pdo->prepare("UPDATE offers SET status = 'Rejected' WHERE id = ?")->execute([$offer_id]);
        $pdo->prepare("INSERT INTO logs (actor_type, actor_id, action, context) VALUES ('user', ?, 'Rejected Offer', ?)")
            ->execute([$user_id, "Rejected offer ID $offer_id for Trade ID $trade_id"]);

        header("Location: trade_detail.php?id=$trade_id&rejected=$offer_id");
        exit();
    }
}

// Fetch vehicle info
$vehicleStmt = $pdo->prepare("SELECT v.* FROM vehicles v WHERE id = ?");
$vehicleStmt->execute([$trade['vehicle_id']]);
$vehicle = $vehicleStmt->fetch();

// Fetch offers
$offerStmt = $pdo->prepare("
    SELECT o.*, d.company_name AS dealer_name 
    FROM offers o 
    JOIN dealers d ON o.dealer_id = d.id 
    WHERE o.trade_id = ?
    ORDER BY o.created_at DESC
");
$offerStmt->execute([$trade_id]);
$offers = $offerStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trade Details | GariHub</title>
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
        }

        .vehicle-box {
            background: #fff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .vehicle-box img {
            width: 180px;
            float: left;
            margin-right: 20px;
            border-radius: 5px;
        }

        .offers-table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            box-shadow: 0 0 6px rgba(0,0,0,0.05);
        }

        .offers-table th, .offers-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .offers-table th {
            background-color: #708A58;
            color: white;
        }

        .btn {
            padding: 6px 12px;
            background: #2D4F2B;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background: #FFB823;
            color: #2D4F2B;
        }

        .btn-reject {
            background-color: #999;
            margin-left: 5px;
        }

        .accepted {
            background: #FFB823;
            color: #2D4F2B;
            padding: 6px 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .rejected {
            background: #ccc;
            padding: 6px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h2>Trade Detail</h2>

    <div class="vehicle-box">
        <img src="../uploads/<?= htmlspecialchars($vehicle['image']) ?>" alt="Vehicle Image">
        <h3><?= htmlspecialchars($vehicle['make']) . " " . htmlspecialchars($vehicle['model']) ?> (<?= $vehicle['year'] ?>)</h3>
        <p>Status: <strong><?= htmlspecialchars($trade['status']) ?></strong></p>
        <p>Submitted: <?= htmlspecialchars($trade['submission_date']) ?></p>
    </div>

    <h3>Offers from Dealers</h3>
    <?php if (count($offers) === 0): ?>
        <p>No offers submitted yet.</p>
    <?php else: ?>
        <table class="offers-table">
            <thead>
                <tr>
                    <th>Dealer</th>
                    <th>Offer (KES)</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($offers as $offer): ?>
                    <tr>
                        <td><?= htmlspecialchars($offer['dealer_name']) ?></td>
                        <td><?= number_format($offer['offer_price']) ?></td>
                        <td><?= htmlspecialchars($offer['created_at']) ?></td>
                        <td><?= htmlspecialchars($offer['status']) ?></td>
                        <td>
                            <?php if ($offer['status'] === 'Pending' && $trade['status'] !== 'Approved'): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Accept this offer?');">
                                    <input type="hidden" name="accept_offer_id" value="<?= $offer['id'] ?>">
                                    <button class="btn" type="submit">Accept Offer</button>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Reject this offer?');">
                                    <input type="hidden" name="reject_offer_id" value="<?= $offer['id'] ?>">
                                    <button class="btn btn-reject" type="submit">Reject Offer</button>
                                </form>
                            <?php elseif ($offer['status'] === 'Accepted'): ?>
                                <span class="accepted">✔ Accepted</span>
                            <?php else: ?>
                                <span class="rejected">✖ Rejected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
