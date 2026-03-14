<style>
    :root {
        --sidebar-bg: #0f172a;
        --sidebar-hover: #1e293b;
        --accent-color: #818cf8; /* The purple from your chart */
        --text-gray: #94a3b8;
    }

    .sidebar {
        width: 260px;
        height: 100vh;
        background: var(--sidebar-bg);
        color: white;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        padding: 20px 0;
        transition: all 0.3s;
        z-index: 1000;
    }

    .sidebar .logo {
        padding: 0 25px 30px;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: white;
        text-transform: uppercase;
    }

    .sidebar .logo span {
        color: var(--accent-color);
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        flex-grow: 1;
    }

    .sidebar ul li {
        margin: 4px 15px;
    }

    .sidebar ul li a {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: var(--text-gray);
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        transition: 0.2s;
    }

    /* Active & Hover States */
    .sidebar ul li a:hover {
        background: var(--sidebar-hover);
        color: white;
    }

    .sidebar ul li a.active {
        background: var(--sidebar-hover);
        color: var(--accent-color);
    }

    .sidebar .logout-section {
        padding: 20px 15px;
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    .logout-btn {
        color: #f87171 !important; /* Soft Red */
    }

    /* Content Adjustment - Add this to your main pages */
    .main-content {
        margin-left: 260px;
        padding: 30px;
        background: #f8fafc;
        min-height: 100vh;
    }
</style>

<div class="sidebar">
    <div class="logo">STOCK<span>PRO</span></div>
    
    <ul>
        <?php 
            // Get current page name to highlight active link
            $current_page = basename($_SERVER['PHP_SELF']); 
        ?>
        <li>
            <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                View Products
            </a>
        </li>
        <li>
            <a href="add_product.php" class="<?= ($current_page == 'add_product.php') ? 'active' : '' ?>">
                Add Product
            </a>
        </li>
        <li>
            <a href="add_staff.php" class="<?= ($current_page == 'add_staff.php') ? 'active' : '' ?>">
                Add Staff
            </a>
        </li>
        <li>
            <a href="stock_in.php" class="<?= ($current_page == 'stock_in.php') ? 'active' : '' ?>">
                Stock In
            </a>
        </li>
        <li>
            <a href="stock_out.php" class="<?= ($current_page == 'stock_out.php') ? 'active' : '' ?>">
                Stock Out
            </a>
        </li>
    </ul>

    <div class="logout-section">
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>