<?php


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['full-name'])) {
        // Registration form
        $fullName = $conn->real_escape_string($_POST['full-name']);
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

        // Determine role based on the number of existing users
        $role = "user"; // Default role
        $result = $conn->query("SELECT COUNT(*) AS user_count FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['user_count'] < 2) {
                $role = "admin"; // Assign admin role to the first two users
            }
        }

        // Insert into database
        $sql = "INSERT INTO users (full_name, username, email, password, role) VALUES ('$fullName', '$username', '$email', '$password', '$role')";

        if ($conn->query($sql) === TRUE) {
            echo "Registration successful! You have been assigned the role of $role.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['signInEmail'])) {
        // Sign-in form
        $email = $conn->real_escape_string($_POST['signInEmail']);
        $password = $_POST['signInPassword'];

        // Check if user exists
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.html");
                } else {
                    header("Location: user_dashboard.html");
                }
                exit(); // Stop further script execution after redirection
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No account found with this email.";
        }
    }
}

$conn->close();
?>