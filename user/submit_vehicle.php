<?php
session_start();
require_once '../includes/db.php';
include '../includes/user_sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize input
    $make = $_POST['make'] === 'Other' ? trim($_POST['custom_make']) : $_POST['make'];
    $model = $_POST['model'] === 'Other' ? trim($_POST['custom_model']) : $_POST['model'];
    $year = intval($_POST['year']);
    $mileage = intval($_POST['mileage']);
    $quoted_price = floatval($_POST['quoted_price']);
    $condition = $_POST['vehicle_condition'];

    // File uploads
    $image = $_FILES['image'];
    $document = $_FILES['documents'];

    $imageExt = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    $docExt = strtolower(pathinfo($document['name'], PATHINFO_EXTENSION));

    $allowedImageExts = ['jpg', 'jpeg', 'png'];
    $allowedDocExts = ['pdf', 'jpg', 'jpeg', 'png'];

    // Validate extensions
    if (!in_array($imageExt, $allowedImageExts)) {
        $message = "❌ Invalid image type. Only JPG, JPEG, PNG allowed.";
    } elseif (!in_array($docExt, $allowedDocExts)) {
        $message = "❌ Invalid document type. Only PDF, JPG, JPEG, PNG allowed.";
    } elseif ($image['error'] !== UPLOAD_ERR_OK || $document['error'] !== UPLOAD_ERR_OK) {
        $message = "❌ File upload error. Please try again.";
    } else {
        // Create upload folders if not exist
        if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
        if (!is_dir('../uploads/documents')) mkdir('../uploads/documents', 0755, true);

        // Unique file names
        $imageName = 'vehicle_' . time() . '_' . basename($image['name']);
        $documentName = 'doc_' . time() . '_' . basename($document['name']);

        $imagePath = '../uploads/' . $imageName;
        $documentPath = '../uploads/documents/' . $documentName;

        move_uploaded_file($image['tmp_name'], $imagePath);
        move_uploaded_file($document['tmp_name'], $documentPath);

        // Insert into vehicles
        $stmt = $pdo->prepare("INSERT INTO vehicles (make, model, year, mileage, image, status, owner_id, dealer_id, `condition`)
                               VALUES (?, ?, ?, ?, ?, 'Pending', ?, NULL, ?)");
        $stmt->execute([$make, $model, $year, $mileage, $imageName, $user_id, $condition]);

        $vehicle_id = $pdo->lastInsertId();

        // Insert into trades
        $trade = $pdo->prepare("INSERT INTO trades (vehicle_id, user_id, status, submission_date, quoted_price)
                                VALUES (?, ?, 'Pending', NOW(), ?)");
        $trade->execute([$vehicle_id, $user_id, $quoted_price]);

        // Insert into documents
        $doc = $pdo->prepare("INSERT INTO documents (vehicle_id, document_type, file_path, uploaded_by, uploaded_at)
                              VALUES (?, 'Ownership', ?, ?, NOW())");
        $doc->execute([$vehicle_id, $documentName, $user_id]);

        // Insert log
        $log = $pdo->prepare("INSERT INTO logs (actor_type, actor_id, action, context)
                              VALUES ('user', ?, 'Submitted Vehicle for Trade', ?)");
        $context = "Trade-in submitted: $make $model ($year), Condition: $condition";
        $log->execute([$user_id, $context]);

        header("Location: trade.php?success=1");
        exit();
    }
}
?>

<!-- HTML FORM SECTION -->
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
    input, select {
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
    .error {
      background: #ffe0e0;
      padding: 10px;
      margin-top: 10px;
      border-left: 5px solid red;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Submit Your Vehicle for Trade-In</h2>

  <?php if (!empty($message)): ?>
    <div class="error"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

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

    <label for="vehicle_condition">Vehicle Condition:</label>
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
    <input type="file" name="documents" accept=".pdf,.jpg,.jpeg,.png" required>

    <button type="submit">Submit Vehicle</button>
  </form>
</div>

<script>
const makeSelect = document.getElementById('make');
const modelSelect = document.getElementById('model');
const customMakeInput = document.getElementById('custom_make');
const customModelInput = document.getElementById('custom_model');

const models = {
  Toyota: ['Axio', 'Fielder', 'Premio', 'Probox', 'Vitz'],
  Nissan: ['Note', 'X-Trail', 'Juke', 'Sylphy'],
  Mazda: ['Demio', 'Axela', 'CX-5'],
  Subaru: ['Forester', 'Outback', 'Legacy'],
  Mitsubishi: ['Lancer', 'Pajero', 'Outlander'],
  Honda: ['Fit', 'Civic', 'Vezel']
};

makeSelect.addEventListener('change', function () {
  const selected = this.value;
  modelSelect.innerHTML = '<option value="">-- Select Model --</option>';

  if (selected === 'Other') {
    customMakeInput.classList.remove('hidden');
    customModelInput.classList.remove('hidden');
    modelSelect.classList.add('hidden');
  } else {
    customMakeInput.classList.add('hidden');
    modelSelect.classList.remove('hidden');
    customModelInput.classList.add('hidden');
    if (models[selected]) {
      models[selected].forEach(model => {
        modelSelect.innerHTML += `<option value="${model}">${model}</option>`;
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
