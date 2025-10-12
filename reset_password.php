
<?php
// Start the session
session_start();

// Check if the token is provided in the URL
if (!isset($_GET['token'])) {
    echo "Invalid or missing token.";
    exit;
}

$token = $_GET['token'];

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'your_database_name');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify the token and check if it has expired
$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Invalid or expired token.";
    exit;
}

$row = $result->fetch_assoc();
$email = $row['email'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate passwords
    if ($newPassword !== $confirmPassword) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the user's password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    $stmt->execute();

    // Delete the token from the password_resets table
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    echo "Your password has been reset successfully.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    
    <!-- Link to styles.css -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <form action="" method="post" id="resetPasswordForm">
            <h2>Reset Password</h2>
            <div class="main">
                <div class="input-div">
                    <label for="password">New Password</label>
                    <input type="password" name="password" placeholder="Enter new password" id="password" required>
                </div>
                <div class="input-div">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" id="confirm_password" required>
                </div>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>