
-- Database: GariHub

-- Drop tables if they exist
DROP TABLE IF EXISTS support;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS trades;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS dealers;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS admins;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    kyc_document VARCHAR(255)
);

-- Dealers Table
CREATE TABLE dealers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    location VARCHAR(100),
    pin_certificate VARCHAR(255),
    license_document VARCHAR(255)
);

-- Admins Table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Vehicles Table
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year YEAR NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    mileage INT,
    `condition` VARCHAR(50),
    owner_id INT,
    dealer_id INT,
    image VARCHAR(255),
    status VARCHAR(50),
    label VARCHAR(50),
    FOREIGN KEY (owner_id) REFERENCES users(id),
    FOREIGN KEY (dealer_id) REFERENCES dealers(id)
);

-- Trades Table
CREATE TABLE trades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vehicle_id INT,
    submission_date DATE,
    quoted_price DECIMAL(10,2),
    status VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    dealer_id INT,
    rating INT,
    comment TEXT,
    created_at DATE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (dealer_id) REFERENCES dealers(id)
);

-- Transactions Table
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vehicle_id INT,
    amount DECIMAL(10,2),
    date DATE,
    proof VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- Support Table
CREATE TABLE support (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    status VARCHAR(50),
    created_at DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Dummy Data

INSERT INTO users (name, email, phone, password, kyc_document) VALUES
('John Doe', 'john@example.com', '0712345678', 'hashedpassword1', 'kyc1.pdf'),
('Jane Smith', 'jane@example.com', '0723456789', 'hashedpassword2', 'kyc2.pdf');

INSERT INTO dealers (company_name, contact_person, email, phone, location, pin_certificate, license_document) VALUES
('GariHub Motors', 'Alex Kariuki', 'sales@garihub.co.ke', '0700111222', 'Nairobi', 'cert123.pdf', 'license123.pdf');

INSERT INTO vehicles (make, model, year, price, mileage, `condition`, owner_id, dealer_id, image, status, label) VALUES
('Toyota', 'Axio', 2015, 980000.00, 80000, 'Good', 1, 1, 'axio.jpg', 'Available', 'New Arrival'),
('Mazda', 'Demio', 2017, 870000.00, 60000, 'Very Good', 2, 1, 'demio.jpg', 'Available', '');

INSERT INTO trades (user_id, vehicle_id, submission_date, quoted_price, status) VALUES
(1, 1, '2025-06-01', 950000.00, 'Pending'),
(2, 2, '2025-06-05', 850000.00, 'Approved');

INSERT INTO reviews (user_id, dealer_id, rating, comment, created_at) VALUES
(1, 1, 5, 'Great service!', '2025-06-10'),
(2, 1, 4, 'Smooth trade-in process.', '2025-06-12');

INSERT INTO transactions (user_id, vehicle_id, amount, date, proof) VALUES
(1, 1, 950000.00, '2025-06-08', 'proof1.jpg'),
(2, 2, 850000.00, '2025-06-10', 'proof2.jpg');

INSERT INTO support (user_id, message, status, created_at) VALUES
(1, 'How do I upload my KYC document?', 'Resolved', '2025-06-09'),
(2, 'Can I trade in two vehicles?', 'Pending', '2025-06-13');
