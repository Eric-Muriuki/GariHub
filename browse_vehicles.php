<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Handle filters (optional)
$where = [];
$params = [];

if (!empty($_GET['make'])) {
    $where[] = "make = ?";
    $params[] = $_GET['make'];
}
if (!empty($_GET['model'])) {
    $where[] = "model = ?";
    $params[] = $_GET['model'];
}
if (!empty($_GET['price'])) {
    $range = explode('-', $_GET['price']);
    if (count($range) == 2) {
        $where[] = "price BETWEEN ? AND ?";
        $params[] = $range[0];
        $params[] = $range[1];
    }
}

$sql = "SELECT * FROM vehicles WHERE status = 'Available'";
if (!empty($where)) {
    $sql .= " AND " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC LIMIT 20";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Browse Vehicles - GariHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f5f5f5;
    }
    h1 {
      text-align: center;
      padding: 20px 0;
      color: #2D4F2B;
    }
    .search-bar {
      background: white;
      padding: 20px;
      text-align: center;
    }
    .search-bar select, .search-bar button {
      padding: 10px;
      margin: 5px;
      width: 100%;
      max-width: 200px;
    }

    .vehicle-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .vehicle-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      overflow: hidden;
      text-align: center;
    }
    .vehicle-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
    }
    .vehicle-card h4 {
      margin: 10px 0 5px;
    }
    .vehicle-card p {
      margin: 5px 0;
      color: #555;
    }
    .vehicle-card button {
      background: #2D4F2B;
      color: white;
      border: none;
      padding: 10px 15px;
      margin-bottom: 15px;
      border-radius: 5px;
      cursor: pointer;
    }

    footer {
      text-align: center;
      background: #2D4F2B;
      color: #FFF1CA;
      padding: 20px;
    }
  </style>
</head>
<body>

<h1>Browse Available Vehicles</h1>

<section class="search-bar">
  <form method="GET" action="browse_vehicles.php">
    <select name="make">
      <option value="">Make</option>
      <option value="Toyota">Toyota</option>
      <option value="Mazda">Mazda</option>
      <option value="Nissan">Nissan</option>
    </select>
    <select name="model">
      <option value="">Model</option>
      <option value="Axio">Axio</option>
      <option value="Demio">Demio</option>
      <option value="Note">Note</option>
    </select>
    <select name="price">
      <option value="">Price Range</option>
      <option value="500000-800000">KES 500K - 800K</option>
      <option value="800000-1200000">KES 800K - 1.2M</option>
      <option value="1200000-2000000">KES 1.2M - 2M</option>
    </select>
    <button type="submit">Search</button>
  </form>
</section>

<section class="vehicle-grid">
  <?php if (count($vehicles) > 0): ?>
    <?php foreach ($vehicles as $v): ?>
      <div class="vehicle-card">
        <img src="uploads/<?= htmlspecialchars($v['image']) ?>" alt="<?= $v['make'] ?>">
        <h4><?= htmlspecialchars($v['make']) ?> <?= htmlspecialchars($v['model']) ?></h4>
        <p><?= number_format($v['price'], 2) ?> KES</p>
        <button onclick="window.location.href='product_detail.php?id=<?= $v['id'] ?>'">View</button>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center;">No vehicles found matching your search.</p>
  <?php endif; ?>
</section>

<footer>
  <p>&copy; <?= date('Y') ?> GariHub. All Rights Reserved.</p>
</footer>

</body>
</html>
