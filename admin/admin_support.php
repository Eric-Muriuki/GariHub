<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'], $_POST['new_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['new_status'];

    $stmt = $pdo->prepare("UPDATE support SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $ticket_id]);
}

// Fetch all support messages
$stmt = $pdo->prepare("
    SELECT support.*, users.name AS user_name, users.email
    FROM support
    JOIN users ON support.user_id = users.id
    ORDER BY support.created_at DESC
");
$stmt->execute();
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Support Center</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
    }

    .header {
      background-color: #2D4F2B;
      color: #fff;
      padding: 20px;
      text-align: center;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: #fff;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #FFF1CA;
    }

    form.inline-form {
      display: inline;
    }

    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-primary {
      background-color: #FFB823;
      color: #2D4F2B;
    }

    .status-pending {
      color: #e67e22;
    }

    .status-resolved {
      color: #27ae60;
    }
  </style>
</head>
<body>

<div class="header">
  <h1>Support Tickets - Admin Panel</h1>
</div>

<div class="container">
  <h2>All Support Requests</h2>

  <?php if (count($tickets) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>User</th>
          <th>Email</th>
          <th>Message</th>
          <th>Status</th>
          <th>Submitted On</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tickets as $ticket): ?>
        <tr>
          <td><?= htmlspecialchars($ticket['user_name']) ?></td>
          <td><?= htmlspecialchars($ticket['email']) ?></td>
          <td><?= nl2br(htmlspecialchars($ticket['message'])) ?></td>
          <td class="<?= $ticket['status'] === 'Resolved' ? 'status-resolved' : 'status-pending' ?>">
              <?= htmlspecialchars($ticket['status']) ?>
          </td>
          <td><?= htmlspecialchars($ticket['created_at']) ?></td>
          <td>
            <form method="POST" class="inline-form">
              <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
              <select name="new_status">
                <option value="Pending" <?= $ticket['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Resolved" <?= $ticket['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
              </select>
              <button type="submit" class="btn btn-primary">Update</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No support requests found.</p>
  <?php endif; ?>
</div>

</body>
</html>
