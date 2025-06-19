<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Fetch dealers user has interacted with
$dealerQuery = $pdo->prepare("
    SELECT DISTINCT d.id, d.company_name 
    FROM dealers d
    JOIN vehicles v ON v.dealer_id = d.id
    JOIN trades t ON t.vehicle_id = v.id
    WHERE t.user_id = ?
");
$dealerQuery->execute([$user_id]);
$dealers = $dealerQuery->fetchAll();

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dealer_id = $_POST['dealer_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($dealer_id && $rating && $comment) {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, dealer_id, rating, comment, created_at)
                               VALUES (?, ?, ?, ?, NOW())");
        if ($stmt->execute([$user_id, $dealer_id, $rating, $comment])) {
            $success = "Review submitted successfully!";
        } else {
            $error = "Failed to submit review.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Review | GariHub</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .main-content {
            margin-left: 240px;
            padding: 30px;
        }
        h2 {
            color: #2D4F2B;
        }
        .form-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        select, textarea, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        textarea {
            resize: vertical;
            height: 120px;
        }
        .btn {
            background-color: #708A58;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .success {
            background-color: #e2f9e1;
            color: #2d6a2d;
        }
        .error {
            background-color: #fde2e2;
            color: #b30000;
        }
        .rating-stars {
            display: flex;
            gap: 10px;
        }
        .rating-stars input {
            display: none;
        }
        .rating-stars label {
            font-size: 24px;
            cursor: pointer;
            color: #ccc;
        }
        .rating-stars input:checked ~ label,
        .rating-stars label:hover,
        .rating-stars label:hover ~ label {
            color: #FFB823;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h2>Submit a Review</h2>

    <div class="form-box">
        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="dealer_id">Select Dealer</label>
            <select name="dealer_id" required>
                <option value="">-- Select Dealer --</option>
                <?php foreach ($dealers as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['company_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="rating">Rating</label>
            <div class="rating-stars">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>">
                    <label for="star<?= $i ?>">★</label>
                <?php endfor; ?>
            </div>

            <label for="comment">Comment</label>
            <textarea name="comment" placeholder="Write your review..." required></textarea>

            <button type="submit" class="btn">Submit Review</button>
        </form>
    </div>
</div>

</body>
</html>
