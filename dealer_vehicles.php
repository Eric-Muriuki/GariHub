<?php
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p>Dealer ID missing.</p>";
    exit;
}
$dealer_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE dealer_id = ?");
$stmt->execute([$dealer_id]);
$vehicles = $stmt->fetchAll();
?>

<link rel="stylesheet" href="css/style.css">
<main>
  <h2>Vehicles by Dealer #<?= $dealer_id ?></h2>
  <div class="vehicle-grid">
    <?php foreach ($vehicles as $v): ?>
      <div class="vehicle-card">
        <img src="uploads/<?= htmlspecialchars($v['image']) ?>" alt="Vehicle">
        <h3><?= $v['make'] ?> <?= $v['model'] ?></h3>
        <p><?= $v['year'] ?> • <?= number_format($v['price']) ?> KES</p>
        <a href="product_detail.php?id=<?= $v['id'] ?>" class="btn">View</a>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
