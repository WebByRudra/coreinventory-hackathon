<?php
session_start();
include "db.php";

// Only manager can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'manager'){
    header("Location: index.php");
    exit();
}

// Fetch all products
$products = mysqli_query($conn, "SELECT * FROM products");

// Fetch all locations (optional: define a locations table)
$locations = ["Main Warehouse", "Production Floor", "Rack A", "Rack B"];

if(isset($_POST['product_id'], $_POST['from_location'], $_POST['to_location'], $_POST['quantity'])){
    $product_id = intval($_POST['product_id']);
    $from_location = mysqli_real_escape_string($conn, $_POST['from_location']);
    $to_location = mysqli_real_escape_string($conn, $_POST['to_location']);
    $quantity = intval($_POST['quantity']);

    if($from_location == $to_location){
        $error = "Source and destination cannot be the same!";
    } elseif($quantity <= 0){
        $error = "Enter a valid quantity!";
    } else {
        // Optional: Check stock in source location (if multi-location)
        // For now, assume always enough stock
        
        // Log transfer
        $sql = "INSERT INTO internal_transfers (product_id, from_location, to_location, quantity) 
                VALUES ($product_id, '$from_location', '$to_location', $quantity)";
        if(mysqli_query($conn, $sql)){
            $success = "Stock transfer logged successfully!";
        } else {
            $error = "Error: ".mysqli_error($conn);
        }
    }
}
?>

<h2>Internal Stock Transfer</h2>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
if(isset($success)) echo "<p style='color:green;'>$success</p>";
?>

<form method="POST">
    <select name="product_id" required>
        <option value="">Select Product</option>
        <?php while($p = mysqli_fetch_assoc($products)){ ?>
            <option value="<?php echo $p['id']; ?>"><?php echo $p['name']." (Stock: ".$p['stock'].")"; ?></option>
        <?php } ?>
    </select><br><br>

    <select name="from_location" required>
        <option value="">From Location</option>
        <?php foreach($locations as $loc){ ?>
            <option value="<?php echo $loc; ?>"><?php echo $loc; ?></option>
        <?php } ?>
    </select><br><br>

    <select name="to_location" required>
        <option value="">To Location</option>
        <?php foreach($locations as $loc){ ?>
            <option value="<?php echo $loc; ?>"><?php echo $loc; ?></option>
        <?php } ?>
    </select><br><br>

    <input type="number" name="quantity" placeholder="Quantity" required><br><br>
    <button type="submit">Transfer Stock</button>
</form>

<br>
<a href="manager_dashboard.php">Back to Dashboard</a>