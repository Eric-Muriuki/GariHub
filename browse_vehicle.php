<?php
include 'includes/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM vehicles WHERE status = 'Available' ORDER BY id DESC");
$vehicles = $stmt->fetchAll();
?>

<link rel="stylesheet" href="css/style.css">
<main>
  <h2>Available Vehicles</h2>
  <div class="vehicle-grid">
    <?php foreach ($vehicles as $v): ?>
      <div class="vehicle-card">
        <img src="uploads/<?= htmlspecialchars($v['image']) ?>" alt="<?= $v['make'] ?>" />
        <h3><?= $v['make'] ?> <?= $v['model'] ?></h3>
        <p><?= $v['year'] ?> • <?= number_format($v['price']) ?> KES</p>
        <a href="product_detail.php?id=<?= $v['id'] ?>" class="btn">View Details</a>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
