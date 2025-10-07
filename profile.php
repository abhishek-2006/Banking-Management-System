<?php
// Assuming db.php handles session_start() and $conn definition
include('db.php'); 

// 1. Security Check
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'user'){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = ''; // Initialize message variable

// 2. Fetch User Data
// ⚠️ Security Note: While this SELECT query is safer as $user_id comes from a session variable, 
//    it's good practice to use prepared statements for all queries if possible.
$user_result = $conn->query("SELECT name, email, balance FROM users WHERE id=$user_id");

if ($user_result && $user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    $display_balance = number_format($user['balance'], 2);
} else {
    // Handle case where user data isn't found
    $user = ['name' => 'N/A', 'email' => 'N/A', 'balance' => 'N/A'];
    $display_balance = 'N/A';
    $message = "<p class='message error'>❌ Could not retrieve user data.</p>";
}

// 3. Handle Password Update Submission
if(isset($_POST['update_password'])){
    $new_password_raw = $_POST['new_password'];

    // 🔒 Security Critical: Hash the new password and use prepared statement
    $hashed_password = password_hash($new_password_raw, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    
    if ($stmt) {
        // Bind parameters: s=string (hashed password), i=integer (user_id)
        $stmt->bind_param("si", $hashed_password, $user_id);

        if($stmt->execute()){
            $message = "<p class='message success'>✅ Password updated successfully!</p>";
        } else { 
            $message = "<p class='message error'>❌ Error updating password: " . $stmt->error . "</p>";
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
    <title>Your Profile</title>
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
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 1.8em;
            text-align: center;
        }
        
        /* Heading for the password section */
        h3 {
            color: var(--text-dark);
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.2em;
        }

        /* Profile Data Display */
        .profile-data p {
            padding: 5px 0;
            font-size: 1em;
            border-bottom: 1px dotted #f0f0f0;
        }
        .profile-data strong {
            display: inline-block;
            width: 100px; /* Aligns the data neatly */
            color: var(--primary-color);
        }
        
        /* Form layout (same as Add User) */
        form {
            margin-bottom: 20px;
        }

        label {
            font-size: 0.9em;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
            display: block;
        }
        
        /* Remove old <br> tags */
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
        
        <?php echo $message; // Display success/error messages ?>

        <h2>👤 Your Profile</h2>
        
        <div class="profile-data">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Balance:</strong> ₹<?php echo $display_balance; ?></p>
        </div>

        <h3>Change Password</h3>
        
        <form method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            
            <button type="submit" name="update_password">Update Password</button>
        </form>

        <br>
        <a href="user_dashboard.php" class="back-link">🏠 Back to Dashboard</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>