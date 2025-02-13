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

    // Fetching year range from the form
    $from_year = isset($_POST['from_year']) ? $_POST['from_year'] : date('Y');
    $to_year = isset($_POST['to_year']) ? $_POST['to_year'] : date('Y');

    if(isset($_POST['view_year'])) {
        // Query to fetch year-wise expenses for the logged-in user
        $sql = "SELECT YEAR(e.date) AS year, SUM(e.expenseAmount) AS total 
                FROM expense e
                INNER JOIN signup s ON e.username = s.username
                WHERE e.username=? AND YEAR(e.date) BETWEEN ? AND ? 
                GROUP BY YEAR(e.date)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $from_year, $to_year);
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
    <title>Year-wise Expenses</title>
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
        button[type="submit"], input[type="number"] {
            background-color: #911faa;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        input[type="number"] {
            background-color: #fff;
            color: black;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body><div class="container">
        <h2>Year-wise Expenses</h2>
        <form method="post">
            From: <input type="number" name="from_year" min="1900" max="9999" step="1" value="<?php echo $from_year; ?>"><br><br>
            To: <input type="number" name="to_year" min="1900" max="9999" step="1" value="<?php echo $to_year; ?>"><br><br>
            <button type="submit" name="view_year">View Year-wise</button><br><br>
        </form>

        <table>
            <tr>
                <th>Year</th>
                <th>Total Amount</th>
            </tr>
            <?php
        // Output data of each row
        if (isset($result) && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["year"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["total"]) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No expenses found for the specified year range</td></tr>";
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
