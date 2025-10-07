<?php
include('db.php');

if(isset($_POST['role'], $_POST['username'], $_POST['password'])){
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($role == 'admin'){
        $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        }
    } else {
        $sql = "SELECT * FROM users WHERE email='$username' AND password='$password'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $user = $result->fetch_assoc();
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = 'user';
            $_SESSION['user_id'] = $user['id'];
            header("Location: user_dashboard.php");
            exit();
        }
    }

    echo "<p>❌ Invalid credentials!</p><a href='index.php'>Back to Login</a>";
}
?>
