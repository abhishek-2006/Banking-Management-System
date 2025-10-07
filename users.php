<?php
include('db.php'); // Assumes db.php includes session_start() and $conn

// 1. Security Check
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

// 2. Fetch User Data
// 🔒 Security Enhancement: Use prepared statements even for simple selects when possible, 
//    although here the query is static and safe.
$sql = "SELECT id, name, email, balance FROM users ORDER BY id ASC";
$result = $conn->query($sql);

$message = '';
if (!$result) {
    $message = "<p class='message error'>❌ Database Error: Could not retrieve users.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>
    <link rel="stylesheet" href="styles.css"> 
    
    <style>
        /* Ensure the body allows the content (table) to stretch wide */
        body {
            min-height: 100vh; 
            padding: 40px 0;
            align-items: center; 
            justify-content: flex-start; /* Align content to the top of the viewport */
        }
        
        /* Adjust the container to a wider size for the table content */
        .container {
            max-width: 800px; /* Wider card to fit the table data */
            padding: 40px;
            text-align: left;
            margin: 20px auto; 
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 1.8em;
            text-align: center;
        }
        
        /* --- Modern Table Styling (Common to history.php) --- */
        table {
            width: 100%;
            border-collapse: collapse; 
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden; 
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            font-size: 1em;
            border: none;
        }

        th {
            background-color: var(--primary-color); 
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f8f8f8; 
        }

        tr:hover {
            background-color: #e0f2f1; 
            cursor: default;
        }
        
        /* Highlight Balance */
        .balance-cell {
            font-weight: 600;
            color: var(--primary-color);
            text-align: right !important; /* Right-align numbers */
        }
        
        /* Back to Dashboard Link Styling (copied from other files) */
        .back-link {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            margin-top: 25px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.2s, color 0.2s;
            font-size: 0.95em;
        }

        .back-link:hover {
            background-color: #e0f2f1;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $message; ?>

        <h2>👥 Registered Users</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <table> 
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="balance-cell" style="text-align: left;">Balance (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($row = $result->fetch_assoc()): 
                        $display_balance = number_format($row['balance'], 2);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="balance-cell">
                                <?php echo $display_balance; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="message success" style="text-align:center;">
                No users currently registered in the system.
            </p>
        <?php endif; ?>
        
        <br>
        <a href="admin_dashboard.php" class="back-link">🏠 Back to Dashboard</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>