<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>GariHub - Buy, Sell or Trade</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f5f5f5;
    }
    .hero {
      background: url('images/hero.jpg') center/cover no-repeat;
      padding: 80px 20px;
      color: white;
      text-align: center;
      background-color: #708A58;
    }
    .hero h1 { font-size: 2.5rem; margin-bottom: 10px; }
    .hero p { font-size: 1.2rem; margin-bottom: 20px; }
    .hero .btn {
      background: #FFB823;
      color: #2D4F2B;
      border: none;
      padding: 10px 20px;
      font-size: 1rem;
      border-radius: 5px;
    }

    .search-section {
      background: #fff;
      padding: 20px;
      text-align: center;
    }
    .search-section select, .search-section button {
      padding: 10px;
      margin: 5px;
    }

    .features {
      display: flex;
      justify-content: space-around;
      padding: 40px 10px;
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

    .vehicles {
      padding: 40px 20px;
    }
    .vehicles h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .vehicle-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    .vehicle-card {
      background: #fff;
      padding: 15px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .vehicle-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    .vehicle-card button {
      margin-top: 10px;
      background: #2D4F2B;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
    }

    .how-it-works {
      background: #fff;
      padding: 40px 20px;
      text-align: center;
    }
    .how-it-works h2 {
      margin-bottom: 30px;
    }
    .steps {
      display: flex;
      justify-content: center;
      gap: 40px;
    }
    .step {
      flex: 1;
      max-width: 250px;
    }

    .testimonials {
      background: #708A58;
      color: white;
      padding: 40px 20px;
    }
    .testimonial-slider {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
    }
    .testimonial {
      scroll-snap-align: start;
      flex: 0 0 300px;
      background: #2D4F2B;
      margin-right: 20px;
      padding: 20px;
      border-radius: 10px;
    }

    .cta {
      background: #FFB823;
      color: #2D4F2B;
      text-align: center;
      padding: 40px 20px;
    }
    .cta .btn {
      background: #2D4F2B;
      color: white;
      border: none;
      padding: 10px 20px;
      margin-top: 10px;
      border-radius: 5px;
    }

    footer {
      text-align: center;
      padding: 30px 10px;
    }
  </style>
</head>
<body>



<section class="hero">
  <h1>Buy, Sell or Trade Your Car with Ease</h1>
  <p>Safe, Transparent and Fast Transactions</p>
  <a href="browse_vehicles.php" class="btn">Start Now</a>
</section>

<section class="search-section">
  <form>
    <select name="make"><option value="">Select Make</option></select>
    <select name="model"><option value="">Select Model</option></select>
    <select name="price"><option value="">Price Range</option></select>
    <button type="submit">Search</button>
  </form>
</section>

<section class="features">
  <div class="feature-card">
    <h3>🔍 Transparent Pricing</h3>
    <p>No hidden fees. Know the price upfront.</p>
  </div>
  <div class="feature-card">
    <h3>✅ Verified Dealers</h3>
    <p>All sellers are KYC-verified for safety.</p>
  </div>
  <div class="feature-card">
    <h3>⚡ Fast Transactions</h3>
    <p>We process your deal quickly and efficiently.</p>
  </div>
  <div class="feature-card">
    <h3>🔐 Safe Payments</h3>
    <p>Payments handled securely via our platform.</p>
  </div>
</section>

<section class="vehicles">
  <h2>Recently Added Vehicles</h2>
  <div class="vehicle-grid">
    <div class="vehicle-card">
      <img src="images/axio.jpg" alt="Axio">
      <h4>Toyota Axio</h4>
      <p>KES 980,000</p>
      <button>View</button>
    </div>
    <div class="vehicle-card">
      <img src="images/demio.jpg" alt="Demio">
      <h4>Mazda Demio</h4>
      <p>KES 870,000</p>
      <button>View</button>
    </div>
    <!-- Add more cards -->
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
  <a href="trade.php" class="btn">Trade Your Car Now</a>
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
