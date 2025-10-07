<?php
// Assuming db.php handles session_start() and $conn definition
include('db.php'); 

// Check for session and role (Crucial security check)
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'user'){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the query returns a result before trying to access fetch_assoc()
$result = $conn->query("SELECT balance FROM users WHERE id=$user_id");

// Check for query success and if a row was returned
if ($result && $result->num_rows > 0) {
    $balance = number_format($result->fetch_assoc()['balance'], 2); // Format balance for clean display
} else {
    // Fallback if balance cannot be found (should ideally not happen)
    $balance = 'N/A';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css"> 
    
    <style>
        /* Ensure body centering is active (inherited from styles.css) */
        body {
            min-height: 100vh; 
            padding: 0;
            align-items: center; /* Center vertically */
            justify-content: center; /* Center horizontally */
        }

        /* Adjust the container max-width for the user menu card */
        .container {
            max-width: 450px;
            padding: 40px; 
            text-align: left;
            margin: 0; 
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 20px; 
            font-weight: 700;
            font-size: 1.8em;
            text-align: center; 
        }

        /* Balance Display Style - Unique feature for this page */
        .balance-display {
            background-color: #e0f2f1; /* Light teal background */
            color: var(--primary-color);
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            font-size: 1.2em;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: block; /* Make it take full width */
        }
        
        /* Dashboard Menu Styling */
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
        }

        li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1em;
            color: var(--text-dark);
            background-color: var(--background-light); 
            border-radius: 10px;
            transition: background-color 0.3s, color 0.3s, transform 0.2s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        li a:hover {
            background-color: #e0f2f1;
            color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Logout Button Specific Styling */
        .logout-link {
            background-color: var(--danger-color);
            color: white !important;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(229, 57, 53, 0.3);
            justify-content: center; 
        }

        .logout-link:hover {
            background-color: #c62828;
            color: white !important;
            box-shadow: 0 6px 15px rgba(229, 57, 53, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👋 Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        
        <p class="balance-display">
            💵 Current Balance: ₹<?php echo $balance; ?>
        </p>
        
        <ul>
            <li><a href="profile.php">👤 View Profile</a></li>
            <li><a href="transfer.php">💸 Transfer Money</a></li>
            <li><a href="user_history.php">📜 Transaction History</a></li>
            <li><a href="logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>