<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "welcome";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve username from session
    $username = $_SESSION['username'];

    // Fetch previously stored data
    $query_prev_data = "SELECT * FROM savings WHERE username = :username";
    $stmt_prev_data = $conn->prepare($query_prev_data);
    $stmt_prev_data->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt_prev_data->execute();
    $prev_data = $stmt_prev_data->fetchAll(PDO::FETCH_ASSOC);

    if ($prev_data) {
        // Display previously stored data
        echo "<h2>Previously Stored Data:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Month</th><th>Year</th><th>Monthly Salary</th><th>Expected Savings</th><th>Saved Amount</th></tr>";
        foreach ($prev_data as $data) {
            echo "<tr>";
            echo "<td>{$data['month']}</td>";
            echo "<td>{$data['year']}</td>";
            echo "<td>{$data['monthly_salary']}</td>";
            echo "<td>{$data['expected_savings']}</td>";
            echo "<td>{$data['saved_amount']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No previously stored data found.</p>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
