<?php
include('db.php'); // Includes database connection ($conn) and session_start()

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

$message = ''; // Initialize message variable

if(isset($_POST['submit'])){
    // ⚠️ SECURITY CRITICAL: Use prepared statements to prevent SQL Injection
    $name = $_POST['name'];
    $email = $_POST['email'];
    $balance = $_POST['balance'];
    $password_raw = $_POST['password'];

    // Security Best Practice: Hash the password before storing it
    $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO users (name, email, balance, password) VALUES (?, ?, ?, ?)");
    
    // Bind parameters: s=string, s=string, d=double/decimal, s=string
    if ($stmt) {
        $stmt->bind_param("ssds", $name, $email, $balance, $hashed_password);

        if($stmt->execute()){
            $message = "<p class='message success'>✅ User **{$name}** added successfully!</p>";
        } else { 
            $message = "<p class='message error'>❌ Error adding user: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
         $message = "<p class='message error'>❌ Database Error: Could not prepare statement.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="styles.css"> 

    <style>
        /* Ensure the body remains centered (inherited from styles.css) */
        body {
            min-height: 100vh; 
            padding: 0;
            align-items: center; 
            justify-content: center; 
        }

        /* Adjust the container width and enforce centering */
        .container {
            max-width: 450px;
            padding: 40px;
            text-align: left;
        }
        
        h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 1.6em;
            text-align: center;
        }

        /* Label layout: Ensures labels stack cleanly above inputs */
        label {
            font-size: 0.9em;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
            display: block;
        }
        
        /* Remove the effect of the old <br> tags by resetting margin and using the global input styles */
        br { display: none; } 

        /* Back to Dashboard Link Styling */
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
        
        <?php
        // Display any success or error messages
        echo $message;
        ?>

        <h2>➕ Add New User</h2>
        <form method="POST">
            <label for="name">Name:</label> 
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="balance">Balance:</label>
            <input type="number" id="balance" name="balance" required>

            <button type="submit" name="submit">Add User</button>
        </form>
        
        <br>
        <a href="admin_dashboard.php" class="back-link">🏠 Back to Dashboard</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>