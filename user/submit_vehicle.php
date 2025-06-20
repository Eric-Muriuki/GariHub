<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $make = $_POST['make'] === 'Other' ? trim($_POST['custom_make']) : $_POST['make'];
    $model = $_POST['model'] === 'Other' ? trim($_POST['custom_model']) : $_POST['model'];
    $year = intval($_POST['year']);
    $mileage = intval($_POST['mileage']);
    $quoted_price = floatval($_POST['quoted_price']);
    $vehicle_condition = $_POST['vehicle_condition'];

    // Upload files
    $image = $_FILES['image']['name'];
    $document = $_FILES['documents']['name'];
    $imagePath = "../uploads/" . basename($image);
    $documentPath = "../uploads/" . basename($document);

    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    move_uploaded_file($_FILES['documents']['tmp_name'], $documentPath);

    // Insert into vehicles table
    $stmt = $pdo->prepare("INSERT INTO vehicles (make, model, year, mileage, image, status, owner_id, dealer_id, `condition`)
                           VALUES (?, ?, ?, ?, ?, 'Pending', ?, NULL, ?)");
    $stmt->execute([$make, $model, $year, $mileage, $image, $user_id, $vehicle_condition]);

    $vehicle_id = $pdo->lastInsertId();

    // Insert into trades table
    $trade = $pdo->prepare("INSERT INTO trades (vehicle_id, user_id, status, submission_date, quoted_price)
                            VALUES (?, ?, 'Pending', NOW(), ?)");
    $trade->execute([$vehicle_id, $user_id, $quoted_price]);

    // Log the action
    $log = $pdo->prepare("INSERT INTO logs (actor_type, actor_id, action, context)
                          VALUES ('user', ?, 'Submitted Vehicle for Trade', ?)");
    $log->execute([$user_id, "Trade-in: $make $model ($year), Condition: $vehicle_condition"]);

    header("Location: trade.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Vehicle</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
    }
    .container {
      max-width: 700px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #2D4F2B;
    }
    form label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .hidden {
      display: none;
    }
    button {
      background: #2D4F2B;
      color: white;
      padding: 12px 20px;
      margin-top: 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #708A58;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Submit Your Vehicle for Trade-In</h2>
  <form method="POST" enctype="multipart/form-data">
    <label for="make">Vehicle Make:</label>
    <select name="make" id="make" required>
      <option value="">-- Select Make --</option>
      <option value="Toyota">Toyota</option>
      <option value="Nissan">Nissan</option>
      <option value="Mazda">Mazda</option>
      <option value="Subaru">Subaru</option>
      <option value="Mitsubishi">Mitsubishi</option>
      <option value="Honda">Honda</option>
      <option value="Other">Other</option>
    </select>
    <input type="text" name="custom_make" id="custom_make" class="hidden" placeholder="Enter custom make">

    <label for="model">Vehicle Model:</label>
    <select name="model" id="model" required>
      <option value="">-- Select Model --</option>
    </select>
    <input type="text" name="custom_model" id="custom_model" class="hidden" placeholder="Enter custom model">

    <label for="year">Year:</label>
    <input type="number" name="year" required>

    <label for="mileage">Mileage (KM):</label>
    <input type="number" name="mileage" required>

    <label for="vehicle_condition">Vehicle Condition (Select):</label>
    <select name="vehicle_condition" required>
      <option value="">-- Select Condition --</option>
      <option value="Excellent">Excellent</option>
      <option value="Good">Good</option>
      <option value="Fair">Fair</option>
      <option value="Poor">Poor</option>
    </select>

    <label for="quoted_price">Expected Price (Ksh):</label>
    <input type="number" name="quoted_price" required>

    <label for="image">Upload Vehicle Image:</label>
    <input type="file" name="image" accept=".jpg,.jpeg,.png" required>

    <label for="documents">Upload Ownership Document:</label>
    <input type="file" name="documents" accept=".pdf,.jpg,.png,.jpeg" required>

    <button type="submit">Submit Vehicle</button>
  </form>
</div>

<script>
const makeSelect = document.getElementById('make');
const modelSelect = document.getElementById('model');
const customMakeInput = document.getElementById('custom_make');
const customModelInput = document.getElementById('custom_model');

// Common makes and models
const models = {
  Toyota: ['Axio', 'Fielder', 'Premio', 'Probox', 'Vitz'],
  Nissan: ['Note', 'X-Trail', 'Juke', 'Sylphy'],
  Mazda: ['Demio', 'Axela', 'CX-5'],
  Subaru: ['Forester', 'Outback', 'Legacy'],
  Mitsubishi: ['Lancer', 'Pajero', 'Outlander'],
  Honda: ['Fit', 'Civic', 'Vezel']
};

makeSelect.addEventListener('change', function () {
  const selectedMake = this.value;
  modelSelect.innerHTML = '<option value="">-- Select Model --</option>';

  if (selectedMake === 'Other') {
    customMakeInput.classList.remove('hidden');
    customModelInput.classList.remove('hidden');
    modelSelect.classList.add('hidden');
  } else {
    customMakeInput.classList.add('hidden');
    customModelInput.classList.add('hidden');
    modelSelect.classList.remove('hidden');

    if (models[selectedMake]) {
      models[selectedMake].forEach(model => {
        const option = document.createElement('option');
        option.value = model;
        option.text = model;
        modelSelect.appendChild(option);
      });
      modelSelect.innerHTML += `<option value="Other">Other</option>`;
    }
  }
});

modelSelect.addEventListener('change', function () {
  if (this.value === 'Other') {
    customModelInput.classList.remove('hidden');
  } else {
    customModelInput.classList.add('hidden');
  }
});
</script>

</body>
</html>

