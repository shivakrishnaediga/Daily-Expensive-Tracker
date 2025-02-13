<?php
session_start(); // Start the session
include 'expense.html'; // Including the HTML form
$servername = "localhost"; // Change this to your database server name
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "welcome"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieving form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $expenseName = $_POST['expenseName'];
    $expenseAmount = $_POST['expenseAmount'];
    $date = $_POST['date'];

    // Retrieving username from session
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        // Inserting form data into database with the associated username
        $sql = "INSERT INTO expense (username, category, expenseName, expenseAmount, date) VALUES ('$username','$category', '$expenseName', '$expenseAmount', '$date')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Username not found in session.";
    }
}
?>
