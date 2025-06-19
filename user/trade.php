<?php
session_start();
require_once '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

include '../includes/user_sidebar.php';

$user_id = $_SESSION['user_id'];

// Fetch user's trades
$stmt = $pdo->prepare("
    SELECT 
        trades.*, 
        vehicles.make, vehicles.model, vehicles.year, vehicles.image 
    FROM trades 
    JOIN vehicles ON trades.vehicle_id = vehicles.id 
    WHERE trades.user_id = ?
    ORDER BY trades.submission_date DESC
");
$stmt->execute([$user_id]);
$trades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Trade-ins | GariHub</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .user-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            height: 100vh;
            background-color: #2D4F2B;
            color: white;
            padding: 20px;
        }

        .main-content {
            margin-left: 240px;
            padding: 30px;
        }

        h2 {
            color: #2D4F2B;
            margin-bottom: 20px;
        }

        .trade-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .trade-table th, .trade-table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .trade-table th {
            background-color: #FFB823;
            color: #2D4F2B;
        }

        .trade-table td img {
            width: 80px;
            border-radius: 4px;
        }

        .btn {
            padding: 6px 12px;
            background-color: #708A58;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .btn:hover {
            background-color: #2D4F2B;
        }

        .no-trades {
            padding: 20px;
            background-color: #FFF1CA;
            color: #2D4F2B;
            border-left: 4px solid #FFB823;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .user-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="main-content">
    <h2>My Vehicle Trade-ins</h2>

    <?php if (count($trades) === 0): ?>
        <div class="no-trades">You haven't submitted any vehicle trade-ins yet.</div>
    <?php else: ?>
        <table class="trade-table">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Submitted</th>
                    <th>Quoted Price (KES)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trades as $trade): ?>
                    <tr>
                        <td>
                            <img src="../uploads/<?= htmlspecialchars($trade['image']) ?>" alt="Vehicle">
                            <br><?= htmlspecialchars($trade['make']) . ' ' . htmlspecialchars($trade['model']) ?> (<?= $trade['year'] ?>)
                        </td>
                        <td><?= htmlspecialchars($trade['submission_date']) ?></td>
                        <td><?= number_format($trade['quoted_price']) ?></td>
                        <td><?= htmlspecialchars($trade['status']) ?></td>
                        <td><a class="btn" href="trade_detail.php?id=<?= $trade['id'] ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    // Optional JS hook for future dynamic actions
</script>

</body>
</html>
