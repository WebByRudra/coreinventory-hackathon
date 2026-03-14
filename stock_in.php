<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// 1. Fetch ALL products and their stock levels. 
// Using LEFT JOIN ensures products like 'Steel Rod' show up even if they have 0 stock in a warehouse.
$inventory_query = "
    SELECT 
        p.id AS product_id, 
        p.name, 
        p.sku, 
        p.category, 
        p.unit, 
        ps.warehouse, 
        IFNULL(ps.stock, 0) as stock
    FROM products p
    LEFT JOIN product_stock_per_warehouse ps ON p.id = ps.product_id
    ORDER BY p.name ASC
";
$result = mysqli_query($conn, $inventory_query);

// 2. Fetch Unique Warehouses for the dropdown.
// This combines warehouses from your 'product_stock' and 'users' table 
// so you can see every available location.
$warehouses_res = mysqli_query($conn, "
    SELECT DISTINCT warehouse FROM product_stock_per_warehouse 
    WHERE warehouse IS NOT NULL AND warehouse != ''
    UNION 
    SELECT DISTINCT warehouse FROM users 
    WHERE warehouse IS NOT NULL AND warehouse != ''
");
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
    <title>Inbound Logistics | CoreStock Pro</title>
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar */
        nav { width: 260px; background: var(--sidebar); color: white; padding: 2rem 1rem; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        nav h2 { font-size: 1.2rem; margin-bottom: 2rem; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; }
        nav a { text-decoration: none; color: #94a3b8; padding: 0.8rem 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        nav a:hover, nav a.active { background: rgba(255,255,255,0.05); color: white; }
        nav a.active { border-left: 3px solid var(--primary); border-radius: 0 8px 8px 0; }
        nav a.logout-btn { margin-top: auto; color: #f87171; border: 1px solid rgba(248,113,113,0.2); justify-content: center; }

        /* Main */
        main { flex: 1; padding: 2.5rem; overflow-y: auto; }
        header { margin-bottom: 2rem; }
        
        .action-card { 
            background: white; padding: 1.5rem; border-radius: 16px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
        }
        
        .stock-form { display: grid; grid-template-columns: 2fr 1.5fr 1fr 0.8fr; gap: 15px; align-items: end; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); }

        select, input {
            padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0;
            background: #f8fafc; outline: none; transition: 0.3s; width: 100%;
        }
        select:focus, input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }

        .add-btn {
            background: var(--primary); color: white; padding: 12px; border: none;
            border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        .add-btn:hover { background: #4f46e5; transform: translateY(-2px); }

        /* Table */
        .table-container { background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .search-container { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        #searchInput { width: 350px; }

        table { width: 100%; border-collapse: collapse; }
        th { padding: 15px 20px; background: #f8fafc; text-align: left; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; }
        td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .low-stock { background: #fef2f2; color: var(--danger); }
        .warehouse-tag { color: var(--primary); font-weight: 600; background: rgba(99, 102, 241, 0.05); padding: 4px 8px; border-radius: 4px; }
    </style>
</head>
<body>

<nav>
    <h2>CoreStock</h2>
    <a href="manager_dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="products.php"><i class="fas fa-boxes"></i> Products</a>
    <a href="stock_in.php" class="active"><i class="fas fa-arrow-circle-down"></i> Stock In</a>
    <a href="stock_out.php"><i class="fas fa-arrow-circle-up"></i> Stock Out</a>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<main>
    <header>
        <h2><i class="fas fa-truck-loading" style="color: var(--primary)"></i> Add Stock to Warehouse</h2>
    </header>

    

    <div class="action-card">
        <form class="stock-form" method="POST" action="update_stock_in.php">
            <div class="form-group">
                <label>Product Details</label>
                <select name="product_id" required>
                    <option value="">-- Choose Product --</option>
                    <?php
                    // Re-run simple query to get all products for the dropdown
                    $dropdown_products = mysqli_query($conn, "SELECT id, name, sku FROM products");
                    while($p = mysqli_fetch_assoc($dropdown_products)){
                        echo "<option value='{$p['id']}'>{$p['name']} [{$p['sku']}]</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Target Warehouse</label>
                <select name="warehouse" required>
                    <option value="">-- Select Location --</option>
                    <?php foreach($warehouses as $w): ?>
                        <option value="<?php echo $w; ?>"><?php echo $w; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" placeholder="0" required min="1">
            </div>

            <button type="submit" class="add-btn">Confirm Stock In</button>
        </form>
    </div>

    <div class="table-container">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search products, SKUs, or warehouses...">
            <p style="font-size: 0.85rem; color: var(--text-muted)">Real-time Inventory Levels</p>
        </div>

        <table id="productsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Warehouse</th>
                    <th>Stock Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600;"><?php echo $row['name']; ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $row['unit']; ?></div>
                    </td>
                    <td style="font-family: monospace; font-weight: bold; color: var(--primary);"><?php echo $row['sku']; ?></td>
                    <td><span class="warehouse-tag"><?php echo $row['warehouse'] ?: 'Unassigned'; ?></span></td>
                    <td style="font-weight: 700;"><?php echo $row['stock']; ?></td>
                    <td>
                        <?php if($row['stock'] < 5): ?>
                            <span class="badge low-stock">LOW</span>
                        <?php else: ?>
                            <span class="badge" style="background: #f1f5f9;">GOOD</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#productsTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
</script>

</body>
</html>