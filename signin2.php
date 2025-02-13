<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "welcome";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve password hash from the database based on username or email
$userid = $_POST['Username']; // Assuming username is posted from a form
$sql = "SELECT password FROM signup WHERE username = '$userid'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Password found, verify it
    $password = $_POST['password']; 
    $row = $result->fetch_assoc();
    $stored_password_hash = $row['password'];
    if ($password == $stored_password_hash) {
        // Store username in session variable
        $_SESSION['username'] = $userid;

        // Redirect to the starting page
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Incorrect password";
    }
} else {
    echo "User not found!";
}

$conn->close();
?>
