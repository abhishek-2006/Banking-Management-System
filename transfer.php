<?php
include('db.php'); // Assumes db.php includes session_start() and $conn

// 1. Authentication and Authorization Check
if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}

$message = ''; // Initialize message variable
$is_admin = ($_SESSION['role'] == 'admin');
$sender_id = $is_admin ? ($_POST['sender_id'] ?? '') : $_SESSION['user_id'];
$initial_sender_id = $sender_id; // Keep track of the initially selected sender ID

// 2. Handle Transfer Submission
if(isset($_POST['transfer'])){
    $sender_id = $is_admin ? $_POST['sender_id'] : $_SESSION['user_id']; // Re-fetch sender_id if it's a POST request
    $receiver_id = $_POST['receiver_id'];
    $amount = $_POST['amount'];
    
    // Validate inputs
    if (empty($sender_id) || empty($receiver_id) || !is_numeric($amount) || $amount <= 0) {
        $message = "<p class='message error'>❌ Invalid sender, receiver, or amount.</p>";
    } elseif ($sender_id == $receiver_id) {
        $message = "<p class='message error'>❌ Sender and Receiver cannot be the same.</p>";
    } else {
        // --- 🔒 Security Critical: Get balance using prepared statement ---
        $stmt_balance = $conn->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt_balance->bind_param("i", $sender_id);
        $stmt_balance->execute();
        $result_balance = $stmt_balance->get_result();
        
        if ($result_balance->num_rows > 0) {
            $current_balance = $result_balance->fetch_assoc()['balance'];
            $stmt_balance->close();

            if ($current_balance >= $amount) {
                // --- 🔒 Security Critical: Use Transaction and Prepared Statements for ALL updates/inserts ---
                $conn->begin_transaction();
                
                try {
                    // Debit Sender
                    $stmt_debit = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                    $stmt_debit->bind_param("di", $amount, $sender_id);
                    $stmt_debit->execute();

                    // Credit Receiver
                    $stmt_credit = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                    $stmt_credit->bind_param("di", $amount, $receiver_id);
                    $stmt_credit->execute();
                    
                    // Log Transaction
                    $stmt_log = $conn->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, date) VALUES (?, ?, ?, NOW())");
                    $stmt_log->bind_param("iid", $sender_id, $receiver_id, $amount);
                    $stmt_log->execute();

                    // Finalize
                    $conn->commit();
                    $message = "<p class='message success'>✅ Transaction of **₹" . number_format($amount, 2) . "** Successful!</p>";

                } catch (Exception $e) {
                    $conn->rollback();
                    $message = "<p class='message error'>❌ Transaction Failed! Database error.</p>";
                    // In a real app, you'd log $e->getMessage()
                }
            } else {
                $message = "<p class='message error'>❌ Transaction Failed! Insufficient balance. Current: ₹" . number_format($current_balance, 2) . "</p>";
            }
        } else {
            $message = "<p class='message error'>❌ Transaction Failed! Sender not found.</p>";
        }
    }
}

// 3. Fetch users for dropdown (used by both sender and receiver lists)
// We fetch them outside the POST block so the form can load
// We need to fetch 'id' and 'name'
$users_result = $conn->query("SELECT id, name, balance FROM users ORDER BY name ASC");
$users_data = $users_result ? $users_result->fetch_all(MYSQLI_ASSOC) : [];

// Function to generate options - UPDATED to include ID
function generate_options($users, $selected_id = null) {
    $options = '';
    foreach ($users as $row) {
        // Use htmlspecialchars for safety
        $name = htmlspecialchars($row['name']);
        $id = htmlspecialchars($row['id']);
        
        $selected = ($selected_id == $row['id']) ? 'selected' : '';
        
        // --- MODIFIED LINE: Include ID in the displayed text ---
        $options .= "<option value='{$id}' {$selected}>{$name} (#{$id})</option>";
    }
    return $options;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transfer Money</title>
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
            max-width: 480px;
            padding: 40px;
            text-align: left;
        }
        
        h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 1.8em;
            text-align: center;
        }

        /* Label layout */
        label {
            font-size: 0.9em;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
            display: block;
        }
        
        /* Remove old <br> tags and use the global input/select styles */
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

        <h2>💸 Transfer Money</h2>
        <form method="POST">
            
            <?php if($is_admin){ ?>
                <label for="sender_id">Sender:</label>
                <select name="sender_id" id="sender_id" required>
                    <option value="">Select Sender</option>
                    <?php echo generate_options($users_data, $initial_sender_id); ?>
                </select>
            <?php } ?>
            
            <label for="receiver_id">Receiver:</label>
            <select name="receiver_id" id="receiver_id" required>
                <option value="">Select Receiver</option>
                <?php echo generate_options($users_data); ?>
            </select>
            
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>

            <button type="submit" name="transfer">Transfer</button>
        </form>
        
        <br>
        <a href="<?php echo $is_admin ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>" class="back-link">🏠 Back to Dashboard</a>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>