<?php
session_start();
include 'db.php';

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

// Fetch stock history with product details if linked, otherwise use your existing table
// I've added a join example in case your database uses product IDs
$query = "SELECT * FROM stock_history ORDER BY date_time DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Ledger | IMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --bg: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.9);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --in-color: #10b981;
            --out-color: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: #f1f5f9;
            color: var(--text-main);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .hero { text-align: center; margin-bottom: 40px; }
        .hero h1 { font-size: 2.2rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .subtitle { color: var(--text-muted); margin-top: 8px; font-size: 1.1rem; }

        .history-container {
            max-width: 1100px;
            margin: 0 auto;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
        }

        .history-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
        .history-table th { 
            padding: 12px 20px; 
            text-align: left; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            color: var(--text-muted);
            font-weight: 700;
        }

        .history-table tbody tr { 
            background: white; 
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        
        .history-table tbody tr:hover { 
            transform: scale(1.01); 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        }

        .history-table td { padding: 18px 20px; font-size: 0.95rem; }
        .history-table td:first-child { border-radius: 12px 0 0 12px; font-weight: 600; color: var(--text-muted); }
        .history-table td:last-child { border-radius: 0 12px 12px 0; color: var(--text-muted); }

        /* Status Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .badge-in { background: #dcfce7; color: var(--in-color); }
        .badge-out { background: #fee2e2; color: var(--out-color); }

        .qty-text { font-family: 'Monaco', 'Consolas', monospace; font-weight: 700; font-size: 1.1rem; }

        .nav-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            transition: 0.2s;
        }
        .nav-back:hover { transform: translateX(-5px); }

        @media (max-width: 768px) {
            .history-table thead { display: none; }
            .history-table td { display: block; text-align: right; padding: 10px 20px; }
            .history-table td::before { content: attr(data-label); float: left; font-weight: 700; color: var(--text-muted); }
        }
    </style>
</head>
<body>

    <div style="max-width: 1100px; margin: 0 auto;">
        <a href="manager_dashboard.php" class="nav-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="hero">
        <h1>Transaction Ledger</h1>
        <p class="subtitle">Complete chronological record of all inventory movements</p>
    </div>

    

    <div class="history-container glass">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Details</th>
                        <th>Movement</th>
                        <th>Quantity</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $is_in = (strtolower($row['type']) == 'in' || strtolower($row['type']) == 'stock in');
                    ?>
                        <tr>
                            <td data-label="ID">#<?php echo $row['id']; ?></td>
                            <td data-label="Product">
                                <div style="font-weight: 700; color: #0f172a;"><?php echo $row['product_name']; ?></div>
                            </td>
                            <td data-label="Type">
                                <span class="badge <?php echo $is_in ? 'badge-in' : 'badge-out'; ?>">
                                    <i class="fas <?php echo $is_in ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'; ?>"></i>
                                    <?php echo $row['type']; ?>
                                </span>
                            </td>
                            <td data-label="Quantity">
                                <span class="qty-text" style="color: <?php echo $is_in ? 'var(--in-color)' : 'var(--out-color)'; ?>">
                                    <?php echo $is_in ? '+' : '-'; ?><?php echo $row['quantity']; ?>
                                </span>
                            </td>
                            <td data-label="Date/Time">
                                <i class="far fa-clock" style="margin-right: 5px; font-size: 0.8rem;"></i>
                                <?php echo date('M d, Y • H:i', strtotime($row['date_time'])); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 0;">
                <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="color: var(--text-muted); font-weight: 500;">No transaction history found in the archives.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>