coreinventory-hackathon

Inventory Management System

CoreStock

CoreStock is a lightweight inventory management system built to track product stock movement in warehouses.
It allows businesses to manage products, record stock entries and exits, monitor inventory history in real time, and get low-stock alerts.

🚀 Features

Product Management

Stock In (Receipts) with Pending Confirmation

Stock Out (Deliveries) with Pending Confirmation

Inventory Movement History

Dashboard Overview with KPIs

Low Stock Alerts

Multi-login: username / email / phone

Simple and Fast Interface

🏗️ Tech Stack

Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Development Environment: XAMPP

📂 Project Structure
corestock/
index.php           → Login page
signup.php          → User signup (manager/staff)
manager_dashboard.php → Manager Dashboard with 4 KPIs
staff_dashboard.php   → Staff Dashboard with 2 KPIs
products.php        → Product listing with low stock alerts
add_product.php     → Add new product
stock_in.php        → Record stock entry (pending + confirm)
confirm_stock_in.php → Confirm stock in
stock_out.php       → Record stock exit (pending + confirm)
confirm_stock_out.php → Confirm stock out
history.php         → Inventory transaction history
logout.php          → Logout session
db.php              → Database connection
sidebar.php         → Navigation sidebar
style.css           → Main styles
script.js           → Client-side scripts
database.sql        → Database schema
README.md
⚙️ Setup Instructions

Install XAMPP

Start Apache and MySQL

Import database.sql in phpMyAdmin

Place project folder inside:

xampp/htdocs/

Open in browser:

http://localhost:8888/corestock
🎯 Use Case

CoreStock helps warehouses and small businesses:

Track incoming inventory (Receipts)

Monitor outgoing deliveries (Deliveries)

Maintain a clear stock ledger

Avoid stock mismatch

Receive alerts for low-stock products

🧩 Workflow Diagram
User (Manager/Staff)
       │
       ▼
  Login / Signup
       │
       ▼
Dashboard → KPIs: Total Products, Low Stock, Pending Receipts, Pending Deliveries
       │
       ├── Products
       │      ├── Add Product
       │      └── View Products (Low Stock Alerts + Search/Filter)
       │
       ├── Stock In (Receipts)
       │      ├── Record Receipt (Pending)
       │      └── Confirm Receipt → Stock Updated
       │
       ├── Stock Out (Delivery)
       │      ├── Record Delivery (Pending)
       │      └── Confirm Delivery → Stock Updated
       │
       ├── Internal Transfers (Planned)
       │
       └── Stock Adjustments (Planned)

⚠ Low stock alerts appear on dashboard and product listings (<5 units).

👨‍💻 Developed For

Hackathon project focused on building a simple but effective inventory tracking system.