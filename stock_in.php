<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Fetch products with their stock per warehouse
$result = mysqli_query($conn, "
    SELECT p.id AS product_id, p.name, p.sku, p.category, p.unit, ps.warehouse, ps.stock, p.created_at
    FROM products p
    LEFT JOIN product_stock_per_warehouse ps ON p.id = ps.product_id
    ORDER BY p.id, ps.warehouse
");

// Fetch all unique warehouses for stock addition
$warehouses_res = mysqli_query($conn, "SELECT DISTINCT warehouse FROM product_stock_per_warehouse");
$warehouses = [];
while($w = mysqli_fetch_assoc($warehouses_res)){
    $warehouses[] = $w['warehouse'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In | CoreStock Pro</title>
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
            --warning: #f59e0b;
            --success: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar Navigation */
        nav { width: 260px; background: var(--sidebar); color: white; padding: 2rem 1rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        nav h2 { font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
        nav a { text-decoration: none; color: #94a3b8; padding: 0.8rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        nav a:hover, nav a.active { background: rgba(255,255,255,0.05); color: white; }
        nav a.back-btn { margin-top: auto; border: 1px solid rgba(255,255,255,0.1); justify-content: center; }

        /* Main Content */
        main { flex: 1; padding: 2.5rem; overflow-y: auto; }
        
        header { margin-bottom: 2rem; }
        header h2 { font-size: 1.75rem; font-weight: 700; display: flex; align-items: center; gap: 12px; }

        /* Action Panel */
        .action-card { 
            background: white; 
            padding: 1.5rem; 
            border-radius: 16px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); 
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
            animation: fadeInDown 0.5s ease-out;
        }
        
        .stock-form { display: grid; grid-template-columns: 2fr 1fr 1fr 0.5fr; gap: 15px; align-items: end; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); }

        select, input[type="number"], #searchInput {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.9rem;
            outline: none;
            transition: 0.3s;
        }

        select:focus, input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }

        button.add-btn {
            background: var(--primary);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        button.add-btn:hover { background: #4f46e5; transform: translateY(-2px); }

        /* Table Section */
        .table-container { 
            background: white; 
            border-radius: 16px; 
            border: 1px solid #e2e8f0; 
            overflow: hidden; 
            animation: fadeInUp 0.6s ease-out;
        }

        .search-container { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        #searchInput { width: 350px; padding-left: 40px; position: relative; }

        table { width: 100%; border-collapse: collapse; }
        th { padding: 15px 20px; background: #f8fafc; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 1px; }
        td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .low-stock { background: #fef2f2; color: var(--danger); border: 1px solid #fee2e2; }
        .warehouse-tag { color: var(--primary); font-weight: 600; background: rgba(99, 102, 241, 0.05); padding: 4px 8px; border-radius: 4px; }

        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav>
    <h2>CoreStock</h2>
    <a href="<?php echo $_SESSION['role']==='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-boxes"></i> Products</a>
    <?php if(trim(strtolower($_SESSION['role'])) === 'manager'): ?>
    <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
    <?php endif; ?>
    <a href="stock_in.php" class="active"><i class="fas fa-arrow-circle-down"></i> Stock In</a>
    <a href="stock_out.php"><i class="fas fa-arrow-circle-up"></i> Stock Out</a>
    
    <a href="<?php echo $_SESSION['role']=='manager' ? 'manager_dashboard.php' : 'staff_dashboard.php'; ?>" class="back-btn">
        <i class="fas fa-chevron-left"></i> <span>Return</span>
    </a>
</nav>

<main>
    <header>
        <h2><i class="fas fa-truck-ramp-box" style="color: var(--primary)"></i> Inbound Stock Management</h2>
    </header>

    <div class="action-card">
        <form class="stock-form" method="POST" action="update_stock_in.php">
            <div class="form-group">
                <label>Select Product</label>
                <select name="product_id" required>
                    <option value="">Search Product...</option>
                    <?php
                    $unique_products = mysqli_query($conn, "SELECT * FROM products");
                    while($p = mysqli_fetch_assoc($unique_products)){
                        echo "<option value='{$p['id']}'>{$p['name']} [{$p['sku']}]</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Warehouse</label>
                <select name="warehouse" required>
                    <option value="">Location...</option>
                    <?php foreach($warehouses as $w){
                        echo "<option value='{$w}'>{$w}</option>";
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" placeholder="0" required min="1">
            </div>

            <button type="submit" class="add-btn">
                <i class="fas fa-plus"></i> Add
            </button>
        </form>
    </div>

    <div class="table-container">
        <div class="search-container">
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 15px; color: var(--text-muted)"></i>
                <input type="text" id="searchInput" placeholder="Search inventory records...">
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted)">Showing all stock locations</p>
        </div>

        <table id="productsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Warehouse</th>
                    <th>Current Stock</th>
                    <th>Alert Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td>
                        <div style="font-weight: 600;"><?php echo $row['name']; ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $row['unit']; ?></div>
                    </td>
                    <td style="font-family: monospace; color: var(--primary); font-weight: bold;"><?php echo $row['sku']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><span class="warehouse-tag"><?php echo $row['warehouse'] ?: 'Default'; ?></span></td>
                    <td style="font-weight: 700; font-size: 1rem;"><?php echo $row['stock'] ?? 0; ?></td>
                    <td>
                        <?php if(($row['stock'] ?? 0) < 5): ?>
                            <span class="badge low-stock"><i class="fas fa-exclamation-triangle"></i> LOW</span>
                        <?php else: ?>
                            <span class="badge" style="background: #f1f5f9; color: var(--text-muted);">NORMAL</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>



<script>
// Improved Live Search
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
        
        // Add a small fade-in effect when rows reappear
        if(text.includes(filter)) {
            row.style.animation = "fadeInUp 0.3s ease-out";
        }
    });
});
</script>

</body>
</html>