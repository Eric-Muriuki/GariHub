
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>GariHub - Buy, Sell or Trade</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f5f5f5;
    }

    header {
      background-color: #2D4F2B;
      color: white;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo i {
      color: #FFB823;
    }

    nav ul {
      list-style: none;
      display: flex;
      margin: 0;
      padding: 0;
      gap: 20px;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      font-size: 1rem;
    }

    nav ul li a:hover {
      color: #FFB823;
    }

    .hero {
      background: url('images/hero.jpg') center/cover no-repeat;
      padding: 100px 20px;
      color: white;
      text-align: center;
      background-color: #708A58;
    }

    .hero h1 {
      font-size: 2.8rem;
      margin-bottom: 10px;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 20px;
    }

    .hero .btn {
      background: #FFB823;
      color: #2D4F2B;
      border: none;
      padding: 12px 25px;
      font-size: 1rem;
      border-radius: 5px;
      text-decoration: none;
    }

    .search-section {
      background: #fff;
      padding: 20px;
      text-align: center;
    }

    .search-section select,
    .search-section button {
      padding: 10px;
      margin: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .features {
      display: flex;
      justify-content: space-around;
      padding: 50px 10px;
      background: #fff;
    }

    .feature-card {
      flex: 1;
      padding: 20px;
      margin: 10px;
      background: #f0f0f0;
      border-radius: 10px;
      text-align: center;
    }

    .feature-card h3 {
      margin-bottom: 10px;
      font-size: 1.3rem;
    }

    .how-it-works {
      background: #fff;
      padding: 50px 20px;
      text-align: center;
    }

    .how-it-works h2,
    .features h2,
    .cta h2,
    .testimonials h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .steps {
      display: flex;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
    }

    .step {
      flex: 1;
      max-width: 250px;
      padding: 10px;
    }

    .testimonials {
      background: #708A58;
      color: white;
      padding: 50px 20px;
    }

    .testimonial-slider {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      gap: 20px;
      padding: 10px 0;
    }

    .testimonial {
      scroll-snap-align: start;
      flex: 0 0 300px;
      background: #2D4F2B;
      padding: 20px;
      border-radius: 10px;
    }

    .cta {
      background: #FFB823;
      color: #2D4F2B;
      text-align: center;
      padding: 50px 20px;
    }

    .cta .btn {
      background: #2D4F2B;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 5px;
      text-decoration: none;
    }

    footer {
      text-align: center;
      padding: 30px 10px;
      font-size: 0.9rem;
    }

    footer a {
      color: #2D4F2B;
      margin: 0 5px;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<header>
  <div class="logo">
    <i class="fas fa-car-side"></i> GariHub
  </div>
  <nav>
    <ul>
      <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="browse_vehicles.php"><i class="fas fa-car"></i> Browse</a></li>
      <li><a href="trade.php"><i class="fas fa-exchange-alt"></i> Trade</a></li>
      <li><a href="support.php"><i class="fas fa-headset"></i> Support</a></li>
      <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
    </ul>
  </nav>
</header>

<section class="hero">
  <h1>Buy, Sell or Trade Your Car with Ease</h1>
  <p>Safe, Transparent and Fast Transactions</p>
  <a href="browse_vehicles.php" class="btn">Start Now</a>
</section>


<section class="features">
  <div class="feature-card">
    <h3><i class="fas fa-tags"></i> Transparent Pricing</h3>
    <p>No hidden fees. Know the price upfront.</p>
  </div>
  <div class="feature-card">
    <h3><i class="fas fa-user-check"></i> Verified Dealers</h3>
    <p>All sellers are KYC-verified for safety.</p>
  </div>
  <div class="feature-card">
    <h3><i class="fas fa-bolt"></i> Fast Transactions</h3>
    <p>We process your deal quickly and efficiently.</p>
  </div>
  <div class="feature-card">
    <h3><i class="fas fa-shield-alt"></i> Safe Payments</h3>
    <p>Payments handled securely via our platform.</p>
  </div>
</section>

<section class="how-it-works">
  <h2>How It Works</h2>
  <div class="steps">
    <div class="step">
      <h3>1️⃣ Submit Vehicle</h3>
      <p>Upload details of the car you want to trade or sell.</p>
    </div>
    <div class="step">
      <h3>2️⃣ Get Offers</h3>
      <p>Receive and compare offers from trusted dealers.</p>
    </div>
    <div class="step">
      <h3>3️⃣ Complete Transaction</h3>
      <p>Upload proof and finalize your deal.</p>
    </div>
  </div>
</section>

<section class="testimonials">
  <h2>What Our Customers Say</h2>
  <div class="testimonial-slider">
    <div class="testimonial">
      <p>"Smooth experience! Sold my car in two days."</p>
      <small>- John Doe</small>
    </div>
    <div class="testimonial">
      <p>"The platform is really transparent and fast."</p>
      <small>- Jane Smith</small>
    </div>
  </div>
</section>

<section class="cta">
  <h2>Got a car to sell or trade?</h2>
  <a href="user/trade.php" class="btn">Trade Your Car Now</a>
</section>

<footer>
  <p>
    <a href="#">About</a> |
    <a href="#">Contact</a> |
    <a href="#">FAQ</a> |
    <a href="#">Policy</a>
  </p>
  <p>Follow us:
    <a href="#">Twitter</a> |
    <a href="#">Instagram</a> |
    <a href="#">Facebook</a>
  </p>
</footer>

</body>
</html>
