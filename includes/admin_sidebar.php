<!-- admin_sidebar.php -->
<style>
  .admin-sidebar {
    width: 220px;
    height: 100vh;
    background: #2D4F2B;
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px;
    color: white;
    margin-left:240px;
  }

  .admin-sidebar h2 {
    text-align: center;
    color: #FFF1CA;
    margin-bottom: 30px;
  }

  .admin-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .admin-sidebar li {
    margin: 15px 0;
  }

  .admin-sidebar a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    display: block;
    padding: 8px;
    border-radius: 4px;
  }

  .admin-sidebar a:hover {
    background-color: #FFB823;
    color: #2D4F2B;
  }
</style>

<div class="admin-sidebar">
  <h2>🛠️ Admin Panel</h2>
  <ul>
    <li><a href="admin_dashboard.php">Dashboard</a></li>
    <li><a href="admin_dealers.php">Manage Dealers</a></li>
    <li><a href="admin_submissions.php">Vehicle Submissions</a></li>
    <li><a href="admin_logs.php">Activity Logs</a></li>
    <li><a href="admin_support.php">Support</a></li>
    <li><a href="admin_reports.php">Reports</a></li>
    <li><a href="../logout.php" style="color: #FFB823;">Logout</a></li>
  </ul>
</div>
