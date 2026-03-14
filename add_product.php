<?php
session_start();
include "db.php";

// Only manager can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != "manager"){
    header("Location: index.php");
    exit();
}

if(isset($_POST['name'], $_POST['sku'], $_POST['category'], $_POST['unit'], $_POST['stock'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $stock = intval($_POST['stock']);

    // Check if SKU already exists
    $check_sku = mysqli_query($conn, "SELECT * FROM products WHERE sku='$sku'");
    if(mysqli_num_rows($check_sku) > 0){
        $error = "SKU '$sku' already exists! Please use a different SKU.";
    } else {
        $sql = "INSERT INTO products (name, sku, category, unit, stock, created_at)
                VALUES ('$name','$sku','$category','$unit',$stock,NOW())";
        if(mysqli_query($conn, $sql)){
            $success = "Product added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | IMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --sidebar: #0f172a;
            --danger: #ef4444;
            --success: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar Navigation */
        nav { width: 260px; background: var(--sidebar); color: white; padding: 2rem 1rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        nav h2 { font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
        nav a { text-decoration: none; color: #94a3b8; padding: 0.8rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        nav a:hover { background: rgba(255,255,255,0.05); color: white; }
        nav a.active { background: var(--primary); color: white; }
        nav a.back-btn { margin-top: auto; color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); justify-content: center; }

        /* Main Content */
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        
        .form-card {
            background: var(--card-bg);
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            animation: scaleUp 0.4s ease-out;
        }

        .form-card h2 { font-size: 1.5rem; margin-bottom: 0.5rem; font-weight: 700; color: var(--text-main); }
        .form-card p.subtitle { color: var(--text-muted); margin-bottom: 2rem; font-size: 0.9rem; }

        /* Input Styles */
        .input-group { margin-bottom: 1.2rem; position: relative; }
        .input-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-main); }
        .input-group i { position: absolute; left: 14px; top: 38px; color: var(--text-muted); }
        
        input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #fcfcfd;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Status Messages */
        .msg { padding: 12px; border-radius: 10px; font-size: 0.9rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; }
        .msg-error { background: #fee2e2; color: var(--danger); border: 1px solid #fecaca; }
        .msg-success { background: #dcfce7; color: var(--success); border: 1px solid #bbf7d0; }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 1rem;
        }

        button:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3); }

        @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>

<nav>
    <h2>IMS Pro</h2>
    <a href="manager_dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-boxes"></i> Products</a>
    <a href="add_product.php" class="active"><i class="fas fa-plus-circle"></i> Add Product</a>
    <a href="stock_in.php"><i class="fas fa-download"></i> Stock In</a>
    <a href="stock_out.php"><i class="fas fa-upload"></i> Stock Out</a>
    <a href="manager_dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
</nav>

<main>
    <div class="form-card">
        <h2>Add New Product</h2>
        <p class="subtitle">Enter product details to update the catalog.</p>

        <?php if(isset($error)): ?>
            <div class="msg msg-error"><i class="fas fa-circle-xmark"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($success)): ?>
            <div class="msg msg-success"><i class="fas fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Product Name</label>
                <i class="fas fa-tag"></i>
                <input type="text" name="name" placeholder="e.g. Wireless Mouse" required>
            </div>

            <div class="input-group">
                <label>SKU (Unique Code)</label>
                <i class="fas fa-barcode"></i>
                <input type="text" name="sku" placeholder="e.g. ELEC-001" required>
            </div>

            <div class="input-group">
                <label>Category</label>
                <i class="fas fa-layer-group"></i>
                <input type="text" name="category" placeholder="e.g. Electronics" required>
            </div>

            <div class="input-group">
                <label>Unit</label>
                <i class="fas fa-weight-hanging"></i>
                <input type="text" name="unit" placeholder="e.g. Pieces / Box" required>
            </div>

            <div class="input-group">
                <label>Initial Stock</label>
                <i class="fas fa-cubes"></i>
                <input type="number" name="stock" placeholder="0" required>
            </div>

            <button type="submit">Create Product</button>
        </form>
    </div>
</main>

</body>
</html>