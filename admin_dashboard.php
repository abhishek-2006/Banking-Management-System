<?php
// Ensure db.php is included to start session and connect to DB (if needed later)
include('db.php'); 

// Check for session and role (Crucial security check)
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> 
    
    <style>
        /*
        The 'styles.css' already sets:*/
        body { 
            display: flex; 
            justify-content: center; /* Centers horizontally */
            align-items: center;     /* Centers vertically */
            min-height: 100vh;
        }
        /*We just need to ensure no conflicting styles are present and adjust the container width.*/
        
        /* Reset any conflicting body styles from the previous version */
        body {
            /* These properties are already in styles.css, but we ensure they are applied */
            min-height: 100vh; 
            padding: 0;
            align-items: center; /* Center the content vertically */
            justify-content: center; /* Center the content horizontally */
        }

        /* Adjust the container max-width and centering for the menu card */
        .container {
            max-width: 500px;
            padding: 40px; /* Increase padding for a better centered card look */
            text-align: left;
            margin: 0; /* Remove any previous margin */
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 30px; /* More space below the header */
            font-weight: 700;
            font-size: 2em;
            letter-spacing: -0.5px;
            text-align: center; /* Center the greeting text */
        }

        /* Dashboard Menu Styling */
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 12px;
        }

        li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
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
            justify-content: center; /* Center the logout text within its button */
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
        <h1>👋 Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <ul>
            <li><a href="add_user.php">➕ Add User</a></li>
            <li><a href="users.php">👥 View All Users</a></li>
            <li><a href="transfer.php">💸 Transfer Money</a></li>
            <li><a href="history.php">📜 Transaction History</a></li>
            <li><a href="logout.php" class="logout-link">🚪 Logout</a></li>
        </ul>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>