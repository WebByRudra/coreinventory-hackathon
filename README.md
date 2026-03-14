CoreInventory Hackathon

CoreInventory is a lightweight Inventory Management System (IMS) built to track product stock movement in warehouses.
It allows businesses to manage products, record stock entries and exits, and monitor inventory in real time with role-based access for managers and staff.

🚀 Features

Product Management → Add, view, and categorize products

Stock In (Receipts) → Increase product stock (Manager only)

Stock Out (Deliveries) → Reduce product stock (Manager & Staff)

Dashboard Overview → Role-based dashboards for manager and staff

User Management → Manager can create staff accounts

Inventory Movement Tracking → Real-time stock updates

Simple and Fast Interface → Easy to use for quick operations

🏗️ Tech Stack

Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Development Environment: XAMPP

📂 Project Structure
coreinventory/

index.php              → Login page
signup.php             → Create new user (manager/staff)
manager_dashboard.php  → Manager dashboard
staff_dashboard.php    → Staff dashboard
products.php           → Product listing
add_product.php        → Add new product
stock_in.php           → Increase stock (Manager only)
stock_out.php          → Reduce stock (Manager & Staff)
add_staff.php          → Create new staff (Manager only)
logout.php             → Logout session

db.php                 → Database connection

style.css              → Main styles
script.js              → Client-side scripts

database.sql           → Database schema

README.md
🧑‍💼 User Roles & Default Credentials
Role	Username	Password	Permissions
Manager	admin	123	Add staff, add product, view products, stock in/out
Staff	ravi	123	View products, stock out only
Staff	sneha	123	View products, stock out only

New users can be created via Sign Up (manager or staff).

⚙️ Setup Instructions

Install XAMPP

Start Apache and MySQL

Import database.sql in phpMyAdmin

Place project folder inside:

xampp/htdocs/

Open in browser:

http://localhost/coreinventory/index.php

Login with default credentials or create new users.

🖥 Dashboard Navigation
Manager

Add Staff → Create staff accounts

Add Product → Add new products

View Products → View full inventory

Stock Out → Reduce stock (product issued/sold)

Stock In → Increase stock (new arrivals/returns)

Logout → End session

Staff

View Products → See inventory

Stock Out → Reduce stock

Logout → End session

🎯 Use Case

CoreInventory helps warehouses and small businesses:

Track incoming inventory

Monitor outgoing deliveries

Maintain a clear stock ledger

Avoid stock mismatch

🔄 Workflow Diagram
        ┌────────────┐
        │  Manager   │
        └─────┬──────┘
              │
   ┌──────────┴──────────┐
   │                     │
Add Staff            Add Product
   │                     │
   ▼                     ▼
 Staff Accounts       Products Table
   │                     │
   └─────────┬───────────┘
             │
        Stock Updates
  ┌──────────┴───────────┐
  │                      │
Stock In (Manager)   Stock Out (Manager/Staff)
  │                      │
  └──────────┬───────────┘
             ▼
      Products Table Updated

This diagram shows manager creating staff/products → stock in/out → inventory updates workflow clearly.

👨‍💻 Developed For

Hackathon project focused on building a simple, functional, role-based IMS for inventory tracking.