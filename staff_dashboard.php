<?php
session_start();
include 'db.php';

// Only staff can access
if(!isset($_SESSION['role']) || trim(strtolower($_SESSION['role'])) != 'staff'){
    header("Location: index.php");
    exit();
}

// Total products
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];

// Low stock products
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock < 5"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Terminal | CoreStock</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --danger: #ef4444;
            --sidebar: #1e293b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar Navigation */
        nav { width: 260px; background: var(--sidebar); color: white; padding: 2rem 1rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        nav h2 { font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
        nav a { text-decoration: none; color: #94a3b8; padding: 0.8rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        nav a:hover, nav a.active { background: rgba(255,255,255,0.05); color: white; }
        nav .logout { margin-top: auto; color: #fda4af; }

        /* Main Content */
        main { flex: 1; padding: 2.5rem; }
        
        .welcome-header { margin-bottom: 2rem; animation: fadeIn 0.5s ease-out; }
        .welcome-header h2 { font-size: 1.8rem; font-weight: 700; }
        .welcome-header p { color: var(--text-muted); margin-top: 4px; }

        /* KPI Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 2.5rem; }
        
        .stat-card { 
            background: var(--card-bg); 
            padding: 1.5rem; 
            border-radius: 20px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card i { font-size: 2rem; padding: 15px; border-radius: 12px; }
        
        .icon-blue { background: #e0e7ff; color: var(--primary); }
        .icon-red { background: #fee2e2; color: var(--danger); }
        
        .stat-info h3 { font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info p { font-size: 1.75rem; font-weight: 800; }

        /* Quick Action Grid */
        .action-section h3 { margin-bottom: 1.5rem; font-size: 1.1rem; color: var(--text-muted); }
        .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        
        .action-btn {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 24px;
            text-align: center;
            text-decoration: none;
            color: var(--text-main);
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .action-btn:hover { border-color: var(--primary); background: #f8faff; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1); }
        .action-btn i { font-size: 2.5rem; color: var(--primary); }
        .action-btn span { font-weight: 600; font-size: 1.1rem; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav>
    <h2>CoreStock Staff</h2>
    <a href="staff_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-boxes"></i> View Products</a>
    <a href="stock_out.php"><i class="fas fa-shipping-fast"></i> Stock Out</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<main>
    <div class="welcome-header">
        <h2>Welcome back, <?php echo $_SESSION['username']; ?> 👋</h2>
        <p>Operational Terminal - <?php echo date('l, d M Y'); ?></p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-layer-group icon-blue"></i>
            <div class="stat-info">
                <h3>Total Products</h3>
                <p><?php echo $total_products; ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-exclamation-circle icon-red"></i>
            <div class="stat-info">
                <h3>Low Stock Items</h3>
                <p><?php echo $low_stock; ?></p>
            </div>
        </div>
    </div>

    <div class="action-section">
        <h3>Quick Operations</h3>
        <div class="action-grid">
            <a href="products.php" class="action-btn">
                <i class="fas fa-search-plus"></i>
                <span>Inventory Lookup</span>
            </a>
            <a href="stock_out.php" class="action-btn">
                <i class="fas fa-dolly"></i>
                <span>Record Delivery</span>
            </a>
        </div>
    </div>
</main>



</body>
</html>