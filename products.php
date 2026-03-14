<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory | IMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --sidebar: #0f172a;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar Navigation */
        nav { width: 260px; background: var(--sidebar); color: white; padding: 2rem 1rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        nav h2 { font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
        nav a { text-decoration: none; color: #94a3b8; padding: 0.8rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        nav a:hover, nav a.active { background: rgba(255,255,255,0.05); color: white; }
        nav a.back-btn { margin-top: auto; background: rgba(99, 102, 241, 0.1); color: var(--primary); justify-content: center; font-weight: 600; }

        /* Main Content */
        main { flex: 1; padding: 2.5rem; overflow-y: auto; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; animation: fadeInDown 0.5s ease-out; }
        .header-flex h2 { font-size: 1.75rem; font-weight: 700; }

        /* Table Styling */
        .table-container { 
            background: var(--card-bg); 
            border-radius: 16px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
            border: 1px solid #e2e8f0;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        
        thead { background: #f1f5f9; }
        th { padding: 15px 20px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; }
        
        td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; color: var(--text-main); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f8fafc; }

        /* Status Badges */
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; display: inline-block; }
        .badge-success { background: #dcfce7; color: var(--success); }
        .badge-warning { background: #fef3c7; color: var(--warning); }
        .badge-danger { background: #fee2e2; color: var(--danger); }

        .sku-text { font-family: 'Courier New', Courier, monospace; color: var(--primary); font-weight: bold; }

        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 1024px) {
            nav { width: 80px; }
            nav h2, nav span { display: none; }
            main { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<nav>
    <h2>IMS</h2>
    <a href="manager_dashboard.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a>
    <a href="products.php" class="active"><i class="fas fa-boxes"></i> <span>Products</span></a>
    <a href="stock_in.php"><i class="fas fa-arrow-down"></i> <span>Stock In</span></a>
    <a href="stock_out.php"><i class="fas fa-arrow-up"></i> <span>Stock Out</span></a>
    
    <a href="<?php echo $_SESSION['role']=='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>" class="back-btn">
        <i class="fas fa-chevron-left"></i> <span>Back</span>
    </a>
</nav>

<main>
    <div class="header-flex">
        <h2>Product Inventory</h2>
        <div class="actions">
            <button onclick="window.print()" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-weight: 600;">
                <i class="fas fa-print"></i> Print List
            </button>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Details</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Stock Level</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { 
                    // Logic for Stock Badges
                    $stock = $row['stock'];
                    $badgeClass = 'badge-success';
                    if($stock <= 0) $badgeClass = 'badge-danger';
                    elseif($stock < 10) $badgeClass = 'badge-warning';
                ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td>
                        <div style="font-weight: 600;"><?php echo $row['name']; ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $row['unit']; ?></div>
                    </td>
                    <td><span class="sku-text"><?php echo $row['sku']; ?></span></td>
                    <td><?php echo $row['category']; ?></td>
                    <td>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo $stock; ?> in stock
                        </span>
                    </td>
                    <td style="color: var(--text-muted); font-size: 0.85rem;">
                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>