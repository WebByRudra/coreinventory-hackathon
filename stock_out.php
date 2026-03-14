<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

$role = strtolower($_SESSION['role']); 

$products = mysqli_query($conn, "
    SELECT p.id AS product_id, p.name, p.sku, p.category, p.unit, ps.warehouse, ps.stock
    FROM products p
    LEFT JOIN product_stock_per_warehouse ps ON p.id = ps.product_id
    ORDER BY p.id, ps.warehouse
");

$warehouses = [];
if($role == 'manager'){
    $warehouses_res = mysqli_query($conn, "SELECT DISTINCT warehouse FROM product_stock_per_warehouse");
    while($w = mysqli_fetch_assoc($warehouses_res)){
        $warehouses[] = $w['warehouse'];
    }
} else if(isset($_SESSION['warehouse'])){
    $staff_warehouse = $_SESSION['warehouse'];
    $warehouses_res = mysqli_query($conn, "SELECT DISTINCT warehouse FROM product_stock_per_warehouse WHERE warehouse='$staff_warehouse'");
    while($w = mysqli_fetch_assoc($warehouses_res)){
        $warehouses[] = $w['warehouse'];
    }
}

if(isset($_POST['product_id'], $_POST['warehouse'], $_POST['quantity'])){
    $product_id = intval($_POST['product_id']);
    $warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);
    $quantity = intval($_POST['quantity']);

    $sql = "INSERT INTO stock_out (product_id, warehouse, quantity, status, created_at)
            VALUES ($product_id, '$warehouse', $quantity, 'pending', NOW())";

    if(mysqli_query($conn, $sql)){
        $success = "Dispatch order created. Confirm to update inventory.";
    } else {
        $error = "Error: ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Out | Dispatch Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --dispatch: #f59e0b; 
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --sidebar: #111827;
            --danger: #ef4444;
            --success: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; letter-spacing: -0.01em; }

        /* Sidebar Navigation */
        nav { width: 280px; background: var(--sidebar); color: white; padding: 2.5rem 1.5rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; box-shadow: 4px 0 10px rgba(0,0,0,0.1); }
        nav h2 { font-size: 1.4rem; font-weight: 800; margin-bottom: 2.5rem; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        nav a { text-decoration: none; color: #9ca3af; padding: 0.9rem 1.2rem; margin-bottom: 0.6rem; border-radius: 12px; display: flex; align-items: center; gap: 14px; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-weight: 500; }
        nav a:hover { background: rgba(255,255,255,0.08); color: white; }
        nav a.active { background: var(--dispatch); color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }

        /* Main Content Area */
        main { flex: 1; padding: 3rem; overflow-y: auto; }
        header { margin-bottom: 3rem; }
        header h2 { font-size: 2rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 15px; }

        /* Modern Dispatch Form Card */
        .dispatch-form-card {
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 3.5rem;
            display: grid;
            grid-template-columns: 1.5fr 1fr 0.6fr auto;
            gap: 24px;
            align-items: end;
            animation: slideInDown 0.6s ease-out;
        }

        .input-group { display: flex; flex-direction: column; gap: 10px; }
        .input-group label { font-size: 0.85rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; }
        
        select, input {
            padding: 14px 16px;
            border-radius: 14px;
            border: 2px solid #f3f4f6;
            background: #f9fafb;
            font-size: 0.95rem;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
        }

        select:focus, input:focus { border-color: var(--dispatch); background: white; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); }

        .dispatch-btn {
            background: var(--dispatch);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }
        .dispatch-btn:hover { background: #d97706; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2); }

        /* Section Heading */
        .section-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; color: var(--text-muted); }

        /* Pending Order Grid */
        .pending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 25px;
            animation: slideInUp 0.7s ease-out;
        }

        .order-card {
            background: white;
            padding: 2rem;
            border-radius: 22px;
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
            transition: all 0.3s ease;
        }
        .order-card:hover { transform: translateY(-8px); border-color: var(--dispatch); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.06); }
        .order-card::after { content: ''; position: absolute; left: 0; top: 20%; height: 60%; width: 5px; background: var(--dispatch); border-radius: 0 5px 5px 0; }

        .order-info h4 { font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
        .order-info p { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; }
        .order-info strong { color: var(--dispatch); }

        .qty-badge { background: #fffbeb; color: #92400e; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 1.3rem; border: 1px solid #fef3c7; }
        
        .confirm-btn {
            flex: 1;
            text-align: center;
            text-decoration: none;
            background: var(--success);
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }
        .confirm-btn:hover { background: #059669; transform: scale(1.02); }

        /* Alert Styling */
        .alert { padding: 16px 20px; border-radius: 16px; margin-bottom: 2.5rem; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 12px; border: 1px solid transparent; }
        .alert-success { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

        @keyframes slideInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 1100px) { .dispatch-form-card { grid-template-columns: 1fr; padding: 2rem; } }
    </style>
</head>
<body>

<nav>
    <h2><i class="fas fa-warehouse"></i> IMS Dispatch</h2>
    <a href="manager_dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-boxes"></i> Inventory List</a>
    <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
    <a href="stock_in.php"><i class="fas fa-arrow-circle-down"></i> Stock In</a>
    <a href="stock_out.php" class="active"><i class="fas fa-shipping-fast"></i> Stock Out</a>
    <a href="<?php echo ($role=='manager') ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>" style="margin-top:auto" class="return-link"><i class="fas fa-long-arrow-alt-left"></i> Return Home</a>
</nav>

<main>
    <header>
        <h2><i class="fas fa-route" style="color: var(--dispatch)"></i> Dispatch Center</h2>
    </header>

    <?php if(isset($error)): ?>
        <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><i class="fas fa-circle-check"></i> <?php echo $success; ?></div>
    <?php endif; ?>

    <form class="dispatch-form-card" method="POST">
        <div class="input-group">
            <label>Product Catalog</label>
            <select name="product_id" required>
                <option value="">Search items...</option>
                <?php
                $unique_products = mysqli_query($conn, "SELECT * FROM products");
                while($p = mysqli_fetch_assoc($unique_products)){
                    echo "<option value='{$p['id']}'>{$p['name']} [{$p['sku']}]</option>";
                }
                ?>
            </select>
        </div>

        <div class="input-group">
            <label>Origin Warehouse</label>
            <select name="warehouse" required>
                <option value="">Location...</option>
                <?php foreach($warehouses as $w){
                    echo "<option value='{$w}'>{$w}</option>";
                } ?>
            </select>
        </div>

        <div class="input-group">
            <label>Release Qty</label>
            <input type="number" name="quantity" placeholder="0" required min="1">
        </div>

        <button type="submit" class="dispatch-btn">
            <i class="fas fa-file-export"></i> Process Dispatch
        </button>
    </form>

    <div class="section-title">
        <i class="fas fa-clock-rotate-left"></i>
        <span>Awaiting Outbound Confirmation</span>
    </div>

    

    <div class="pending-grid">
        <?php
        if($role == 'manager'){
            $pending = mysqli_query($conn, "SELECT so.id, p.name, p.sku, so.quantity, so.warehouse FROM stock_out so JOIN products p ON so.product_id=p.id WHERE so.status='pending' ORDER BY so.created_at DESC");
        } else if(isset($_SESSION['warehouse'])){
            $staff_warehouse = $_SESSION['warehouse'];
            $pending = mysqli_query($conn, "SELECT so.id, p.name, p.sku, so.quantity, so.warehouse FROM stock_out so JOIN products p ON so.product_id=p.id WHERE so.status='pending' AND so.warehouse='$staff_warehouse' ORDER BY so.created_at DESC");
        } else {
            $pending = mysqli_query($conn, "SELECT 1 WHERE 0");
        }

        if(mysqli_num_rows($pending) == 0): ?>
            <div style="grid-column: span 3; text-align: center; padding: 60px; background: white; border-radius: 24px; color: var(--text-muted); border: 2px dashed #e5e7eb;">
                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p style="font-weight: 600;">All shipments are currently clear.</p>
            </div>
        <?php else: 
            while($row = mysqli_fetch_assoc($pending)): ?>
            <div class="order-card">
                <div class="order-info">
                    <h4><?php echo $row['name']; ?></h4>
                    <p>Catalog Code: <strong><?php echo $row['sku']; ?></strong></p>
                    <p>Storage: <strong><?php echo $row['warehouse']; ?></strong></p>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px;">
                    <span class="qty-badge"><?php echo $row['quantity']; ?> <small style="font-size: 0.75rem; vertical-align: middle;">PCS</small></span>
                    <a href="confirm_stock_out.php?id=<?php echo $row['id']; ?>" class="confirm-btn">Confirm Out</a>
                </div>
            </div>
            <?php endwhile; 
        endif; ?>
    </div>
</main>

</body>
</html>