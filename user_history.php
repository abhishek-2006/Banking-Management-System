<?php
include('db.php');

// 1. Security Check
if(!isset($_SESSION['username']) || $_SESSION['role']!='user'){
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Fetch Transaction History
// 🔒 Security Enhancement: Use prepared statements to prevent SQL Injection
$sql = "SELECT t.id, s.name AS sender, r.name AS receiver, t.amount, t.date
        FROM transactions t
        JOIN users s ON t.sender_id = s.id
        JOIN users r ON t.receiver_id = r.id
        WHERE sender_id = ? OR receiver_id = ?
        ORDER BY t.date DESC";

$stmt = $conn->prepare($sql);

// Bind parameters: i=integer (for user_id, twice)
if ($stmt) {
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // Handle statement preparation error
    $result = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Transactions</title>
    <link rel="stylesheet" href="styles.css"> 
    
    <style>
        /* Ensure the body remains centered (inherited from styles.css) */
        body {
            /* We override the body to allow the table to expand a bit more */
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
        
        /* --- Modern Table Styling --- */
        table {
            width: 100%;
            border-collapse: collapse; /* Remove double borders */
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden; /* Ensures shadows/borders apply nicely */
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            font-size: 1em;
            border: none; /* Remove default table borders */
        }

        th {
            background-color: var(--primary-color); /* Teal header background */
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f8f8f8; /* Light gray stripe for readability */
        }

        tr:hover {
            background-color: #e0f2f1; /* Light teal row hover */
            cursor: default;
        }
        
        /* Specific Amount Styling: Color-code for clarity */
        .positive {
            color: var(--primary-color); /* Teal for incoming money */
            font-weight: 600;
        }
        .negative {
            color: var(--danger-color); /* Red for outgoing money */
            font-weight: 600;
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
        <h2>📜 Your Transaction History</h2>

        <?php if ($result && $result->num_rows > 0): ?>
            <table border="0"> <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        // Determine if the transaction is a credit or debit for the current user
                        $is_credit = ($row['receiver'] == $_SESSION['username']);
                        $amount_class = $is_credit ? 'positive' : 'negative';
                        $amount_prefix = $is_credit ? '+ ' : '- ';
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['sender']); ?></td>
                            <td><?php echo htmlspecialchars($row['receiver']); ?></td>
                            <td class="<?php echo $amount_class; ?>">
                                <?php echo $amount_prefix . '₹' . number_format($row['amount'], 2); ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="message success" style="text-align:center;">
                No transactions found yet. Start transferring some money!
            </p>
        <?php endif; ?>

        <br>
        <a href="user_dashboard.php" class="back-link">🏠 Back to Dashboard</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>