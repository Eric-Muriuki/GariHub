<?php
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p>Invalid vehicle ID.</p>";
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT v.*, d.company_name FROM vehicles v LEFT JOIN dealers d ON v.dealer_id = d.id WHERE v.id = ?");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();
?>

<link rel="stylesheet" href="css/style.css">
<main>
  <div class="vehicle-detail">
    <img src="uploads/<?= htmlspecialchars($vehicle['image']) ?>" alt="Vehicle Image">
    <h2><?= $vehicle['make'] ?> <?= $vehicle['model'] ?> (<?= $vehicle['year'] ?>)</h2>
    <p><strong>Price:</strong> <?= number_format($vehicle['price']) ?> KES</p>
    <p><strong>Condition:</strong> <?= $vehicle['condition'] ?></p>
    <p><strong>Dealer:</strong> <?= $vehicle['company_name'] ?? 'Individual Seller' ?></p>
    <a href="dealer-vehicles.php?id=<?= $vehicle['dealer_id'] ?>" class="btn">More from this dealer</a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
