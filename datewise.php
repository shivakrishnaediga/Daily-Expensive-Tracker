<?php
session_start(); // Start the session

// Database connection details
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

// Fetching data from the database for the logged-in user
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];


// Fetching date range from the form
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d');
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : date('Y-m-d');

if(isset($_POST['view_date'])) {
    // Query to fetch date-wise expenses
    $sql = "SELECT * FROM expense WHERE username=? AND date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $from_date, $to_date);
    $stmt->execute();
    $result = $stmt->get_result();
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date-wise Expenses</title>
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
    <h2>Date-wise Expenses</h2>
    <form method="post">
        From: <input type="date" name="from_date"><br><br>
        To: <input type="date" name="to_date"><br><br>
        <button type="submit" name="view_date">View Date-wise</button><br><br>
    </form>

    <table border="1">
        <tr>
            <th>Expense Name</th>
            <th>Expense Amount</th>
            <th>Date</th>
        </tr>
        <?php
        // Output data of each row
        if (isset($result) && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["expenseName"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["expenseAmount"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["date"]) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No expenses found for the specified date range</td></tr>";
        }
        ?>
    </table>
    </div>
</body>
</html>

<?php
// Closing database connection
$conn->close();
?>
