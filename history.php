<?php
include('db.php'); // Assumes db.php includes session_start() and $conn

// 1. Security Check
if(!isset($_SESSION['username']) || $_SESSION['role']!='admin'){
    header("Location: index.php");
    exit();
}

// 2. Fetch Transaction History
// The query is static and does not involve user input, so it's safe from simple injection, 
// but using $conn->query() is fine here.
$sql = "SELECT t.id,s.name AS sender,r.name AS receiver,t.amount,t.date
        FROM transactions t
        JOIN users s ON t.sender_id=s.id
        JOIN users r ON t.receiver_id=r.id
        ORDER BY t.date DESC";

$result = $conn->query($sql);

// Check if the query failed or returned no results
$message = '';
if (!$result) {
    $message = "<p class='message error'>❌ Database Error: Could not retrieve transaction history.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Transactions</title>
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
            max-width: 900px;
            padding: 40px;
            text-align: left;
            margin: 20px auto; /* Center the container on the page */
        }

        h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 1.8em;
            text-align: center;
        }
        
        /* --- Modern Table Styling (Common across all table pages) --- */
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
        
        /* Amount Styling: Consistent look for currency */
        .amount-cell {
            font-weight: 600;
            color: var(--text-dark); /* Neutral color since it's admin view */
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

        <h2>📜 Transaction History</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <table> 
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th class="amount-cell" style="text-align: left;">Amount (₹)</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($row = $result->fetch_assoc()): 
                        $display_amount = number_format($row['amount'], 2);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['sender']); ?></td>
                            <td><?php echo htmlspecialchars($row['receiver']); ?></td>
                            <td class="amount-cell">
                                <?php echo '₹' . $display_amount; ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="message success" style="text-align:center;">
                No transactions have been recorded yet.
            </p>
        <?php endif; ?>
        
        <br>
        <a href="admin_dashboard.php" class="back-link">🏠 Back to Dashboard</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>