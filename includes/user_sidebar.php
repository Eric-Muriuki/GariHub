<!-- Sidebar Toggle Button (for mobile) -->
<button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

<!-- Sidebar -->
<nav class="user-sidebar" id="userSidebar">
    <h2 class="sidebar-title">👤 My Account</h2>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
         <li><a href="submit_vehicle.php">📝 Submit Vehicle</a></li>
        <li><a href="trade.php">🚗 My Trades</a></li>
        <li><a href="transactions.php">💳 Transactions</a></li>
        <li><a href="submit_review.php">⭐ Submit Review</a></li>
        <li><a href="support.php">🛠️ Support</a></li>
        <li><a href="upload_kyc.php">📁 KYC Docs</a></li>
        <li><a href="profile.php">⚙️ Profile</a></li>
        <li><a href="user_logs.php">🕓 My Logs</a></li>
        <li><a href="logout.php" class="logout-link">🚪 Logout</a></li>
    </ul>
</nav>

<style>
/* Sidebar base styles */
.user-sidebar {
  width: 220px;
  background-color: #2D4F2B;
  color: #fff;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  padding: 1rem;
  transition: transform 0.3s ease;
  z-index: 999;
}

.sidebar-title {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #FFF1CA;
  text-align: center;
}

.sidebar-menu {
  list-style: none;
  padding: 0;
}

.sidebar-menu li {
  margin: 15px 0;
}

.sidebar-menu a {
  text-decoration: none;
  color: #fff;
  display: block;
  padding: 0.5rem;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

.sidebar-menu a:hover {
  background-color: #708A58;
}

.logout-link {
  color: #FFB823;
}

/* Sidebar toggle button */
.sidebar-toggle {
  display: none;
  background: #FFB823;
  border: none;
  color: #2D4F2B;
  font-size: 1.2rem;
  padding: 10px 15px;
  margin: 10px;
  border-radius: 5px;
  z-index: 1000;
  position: fixed;
  top: 10px;
  left: 10px;
  cursor: pointer;
}

/* Mobile styles */
@media (max-width: 768px) {
  .user-sidebar {
    transform: translateX(-100%);
  }

  .user-sidebar.sidebar-open {
    transform: translateX(0);
  }

  .sidebar-toggle {
    display: block;
  }
}
</style>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById("userSidebar");
  const toggleBtn = document.querySelector(".sidebar-toggle");

  sidebar.classList.toggle("sidebar-open");

  if (sidebar.classList.contains("sidebar-open")) {
    toggleBtn.textContent = "✖️";
  } else {
    toggleBtn.textContent = "☰";
  }
}
</script>
