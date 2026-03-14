<?php
session_start();
include 'db.php';

// Only manager can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'manager'){
    header("Location: index.php");
    exit();
}

// Stats Queries
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock < 5"))['total'];
$pending_receipts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM stock_in WHERE status='pending'"))['total'];
$pending_deliveries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM stock_out WHERE status='pending'"))['total'];

// Data for Chart (Top 7 products by stock)
$chart_labels = [];
$chart_values = [];
$chart_query = mysqli_query($conn, "SELECT name, stock FROM products ORDER BY stock DESC LIMIT 7");
while($row = mysqli_fetch_assoc($chart_query)) {
    $chart_labels[] = $row['name'];
    $chart_values[] = $row['stock'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard | Inventory Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #4f46e5;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --sidebar: #0f172a;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar */
        nav { width: 260px; background: var(--sidebar); color: white; padding: 2rem 1rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        nav h2 { font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
        nav a { text-decoration: none; color: #94a3b8; padding: 0.8rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; align-items: center; gap: 12px; transition: all 0.2s; }
        nav a:hover, nav a.active { background: rgba(255,255,255,0.05); color: white; transform: translateX(5px); }
        nav a.logout { margin-top: auto; color: #f87171; }

        /* Main Content */
        main { flex: 1; padding: 2rem 3rem; overflow-y: auto; }
        
        /* Header & Search */
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; animation: fadeInDown 0.6s ease-out; gap: 20px; }
        .search-bar { position: relative; flex: 1; max-width: 400px; }
        .search-bar input { width: 100%; padding: 12px 15px 12px 40px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; transition: 0.3s; }
        .search-bar input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .search-bar i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }

        /* KPI Cards */
        .kpi-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 2rem; }
        .kpi { background: var(--card-bg); padding: 1.5rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #e2e8f0; position: relative; overflow: hidden; animation: fadeInUp 0.6s ease-out backwards; }
        .kpi:hover { transform: translateY(-5px); border-color: var(--primary); }
        .kpi h3 { font-size: 0.875rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; }
        .kpi p { font-size: 2rem; font-weight: 700; margin-top: 10px; }
        .kpi i { position: absolute; right: -10px; bottom: -10px; font-size: 4rem; opacity: 0.05; }

        /* Layout Grid for Chart and Alerts */
        .dashboard-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px; align-items: start; }
        .chart-section, .alert-section { background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }

        .alert-item { padding: 12px 15px; border-left: 4px solid var(--danger); background: #fef2f2; margin-bottom: 10px; border-radius: 0 8px 8px 0; display: flex; justify-content: space-between; align-items: center; animation: slideInLeft 0.4s ease-out; }
        .alert-item span.count { background: var(--danger); color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }

        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-15px); } to { opacity: 1; transform: translateX(0); } }

        @media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) { body { flex-direction: column; } nav { width: 100%; height: auto; flex-direction: row; overflow-x: auto; } header { flex-direction: column; align-items: flex-start; } }
    </style>
</head>
<body>

<nav>
    <h2>StockPro</h2>
    <a href="products.php"><i class="fas fa-boxes"></i> View Products</a>
    <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
    <a href="add_staff.php"><i class="fas fa-user-plus"></i> Add Staff</a>
    <a href="stock_in.php"><i class="fas fa-arrow-down"></i> Stock In</a>
    <a href="stock_out.php"><i class="fas fa-arrow-up"></i> Stock Out</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<main>
    <header>
        <div class="welcome-text">
            <h1>Hello, <?php echo $_SESSION['username']; ?></h1>
            <p>System Overview</p>
        </div>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="productSearch" placeholder="Search products...">
        </div>
    </header>

    <div class="kpi-container">
        <div class="kpi" style="animation-delay: 0.1s">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
            <i class="fas fa-box"></i>
        </div>
        <div class="kpi" style="animation-delay: 0.2s">
            <h3>Low Stock</h3>
            <p style="color: var(--danger);"><?php echo $low_stock; ?></p>
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="kpi" style="animation-delay: 0.3s">
            <h3>Pending In</h3>
            <p><?php echo $pending_receipts; ?></p>
            <i class="fas fa-file-import"></i>
        </div>
        <div class="kpi" style="animation-delay: 0.4s">
            <h3>Pending Out</h3>
            <p><?php echo $pending_deliveries; ?></p>
            <i class="fas fa-truck-loading"></i>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="chart-section">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-chart-line" style="color: var(--primary)"></i> Stock Levels (Top Products)</h3>
            <canvas id="stockChart" height="180"></canvas>
        </div>

        <div class="alert-section">
            <h3 style="margin-bottom: 20px;"><i class="fas fa-bell" style="color: var(--warning)"></i> Critical Alerts</h3>
            <div class="alerts-list">
                <?php
                $products_alerts = mysqli_query($conn, "SELECT * FROM products WHERE stock < 5");
                if(mysqli_num_rows($products_alerts) > 0) {
                    while($p = mysqli_fetch_assoc($products_alerts)){
                        echo "<div class='alert-item' data-name='".strtolower($p['name'])."'>
                                <span><i class='fas fa-exclamation-circle'></i> ".$p['name']."</span>
                                <span class='count'>".$p['stock']."</span>
                              </div>";
                    }
                } else {
                    echo "<p style='color: var(--text-muted); font-style: italic;'>Inventory healthy.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</main>

<script>
    // 1. Chart Visualization
    const ctx = document.getElementById('stockChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Stock Quantity',
                data: <?php echo json_encode($chart_values); ?>,
                backgroundColor: 'rgba(99, 102, 241, 0.5)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: '#6366f1'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Real-time Search Logic for Alerts
    document.getElementById('productSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.alert-item');
        
        items.forEach(item => {
            let name = item.getAttribute('data-name');
            item.style.display = name.includes(filter) ? 'flex' : 'none';
        });
    });
</script>

</body>
</html>