<?php
session_start(); // Start the session

// Database connection details (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "welcome";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$from_month;
$to_month;
// Fetching data from the database for the logged-in user
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Handle form submission and data retrieval
    if (isset($_POST['view_month'])) {
        // Fetching year and month range from the form
        $from_month = isset($_POST['from_month']) ? $_POST['from_month'] : date('Y-m');
        $to_month = isset($_POST['to_month']) ? $_POST['to_month'] : date('Y-m');

        // Prepare SQL statement with parameterized query for security
        $sql = "SELECT DATE_FORMAT(e.date, '%M %Y') AS month_year, SUM(e.expenseAmount) AS total 
                FROM expense e
                INNER JOIN signup s ON e.username = s.username
                WHERE e.username=? AND DATE_FORMAT(e.date, '%Y-%m') BETWEEN ? AND ? 
                GROUP BY DATE_FORMAT(e.date, '%Y-%m')";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error in preparing SQL statement: " . $conn->error);
        }

        // Bind parameters to prevent SQL injection vulnerabilities
        $stmt->bind_param("sss", $username, $from_month, $to_month);
        if (!$stmt->execute()) {
            die("Error executing query: " . $stmt->error);
        }

        $result = $stmt->get_result();

        if (!$result) {
            die("Error in getting result set: " . $conn->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Expenses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #911faa;
            color: white;
        }
        button[type="submit"] {
            background-color: #911faa;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        input[type="date"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
    <h2>Monthly Expenses</h2>
    <form method="post">
        From: <input type="month" name="from_month"><br><br>
        To: <input type="month" name="to_month"><br><br>
        <button type="submit" name="view_month">View Monthly</button><br><br>
    </form>

    <table border="1">
        <tr>
            <th>Month</th>
            <th>Total Amount</th>
        </tr>
        <?php
        // Output data of each row
        if (isset($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["month_year"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["total"]) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No expenses found for the specified month range</td></tr>";
        }
        ?>
    </table>
    </div>
    <?php
    // Closing database connection
    $conn->close();
    ?>
</body>
</html>
