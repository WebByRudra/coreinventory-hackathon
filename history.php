<?php
// history.php

// Include database connection
include 'db.php'; // adjust path if needed

// Fetch stock history (example)
$query = "SELECT * FROM stock_history ORDER BY date_time DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stock History</title>
  <link rel="stylesheet" href="style.css">
  <script defer src="script.js"></script>
</head>
<body>

  <div class="hero">
    <h1>Stock History</h1>
    <p class="subtitle">View all your stock in/out activities</p>
  </div>

  <div class="history-container glass">
    <?php if(mysqli_num_rows($result) > 0): ?>
      <table class="history-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Date/Time</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo $row['product_name']; ?></td>
              <td><?php echo $row['type']; ?></td>
              <td><?php echo $row['quantity']; ?></td>
              <td><?php echo $row['date_time']; ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No history found.</p>
    <?php endif; ?>
  </div>

</body>
</html>