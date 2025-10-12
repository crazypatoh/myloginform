<?php
// filepath: c:\xampp\htdocs\MY PROJECT\user_crud.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $sql = "INSERT INTO users (fullname, username, email, password) VALUES ('$fullname', '$username', '$email', '$password')";
        echo $conn->query($sql) ? "User added successfully" : "Error: " . $conn->error;
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        $sql = "UPDATE users SET fullname='$fullname', username='$username', email='$email'";
        if ($password) {
            $sql .= ", password='$password'";
        }
        $sql .= " WHERE id=$id";
        echo $conn->query($sql) ? "User updated successfully" : "Error: " . $conn->error;
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id=$id";
        echo $conn->query($sql) ? "User deleted successfully" : "Error: " . $conn->error;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, fullname, username, email FROM users";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

$conn->close();
?>