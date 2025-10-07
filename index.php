<?php
session_start();
if(isset($_SESSION['username'])){
    if($_SESSION['role'] == 'admin') header("Location: admin_dashboard.php");
    else header("Location: user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mini Bank System</title>
    <link rel="stylesheet" href="styles.css"> 
    
    <style>
        h1 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 2em;
            font-weight: 700;
            letter-spacing: -0.5px;
            text-align: center;
        }
        
        /* Layout adjustments for stacking */
        label {
            font-size: 0.9em;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }
        
        form > * {
            display: block;
            margin-bottom: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>💰 Welcome to Mini Bank System</h1>
    <form method="POST" action="login.php" class="container"> 
        
        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>

        <label for="username">Username/Email:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <?php include('footer.php'); ?>
</body>
</html>