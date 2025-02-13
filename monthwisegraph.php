<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Expenses Line Graph</title>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body{
            background-color:rgb(185, 222, 255);
        }
        #con{
            background-color:#ffffff;
            width:700px;
            height:400px;
            margin-top:40px;
            margin-left:300px;

        }
        h2{
            text-align:center;
        }
    </style>

</head>
<body>
    <h2>Monthly Expenses</h2>
    <div id="con">
    <div>
        <canvas id="expensesChart"></canvas>
    </div>
    </div>

    <?php
    // Connect to MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "welcome";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get username from session
    $username = $_SESSION['username'];

    // Query to retrieve month-wise user expenses data using username
    $stmt = $conn->prepare("SELECT YEAR(e.date) AS year, MONTH(e.date) AS month, SUM(e.expenseAmount) AS total_expense 
                            FROM expense e
                            INNER JOIN signup s ON e.username = s.username
                            WHERE s.username = ? 
                            GROUP BY YEAR(e.date), MONTH(e.date) 
                            ORDER BY YEAR(e.date), MONTH(e.date)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize arrays to store months and total expenses
    $months = [];
    $totalExpenses = [];

    if ($result->num_rows > 0) {
        // Fetching data and populating arrays
        while($row = $result->fetch_assoc()) {
            $months[] = date("M Y", mktime(0, 0, 0, $row["month"], 1, $row["year"]));
            $totalExpenses[] = $row["total_expense"];
        }
    } else {
        echo "No expenses data found.";
    }

    $stmt->close();
    $conn->close();
    ?>

    <script>
        // JavaScript code to create the chart
        var ctx = document.getElementById('expensesChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Expenses Per Month',
                    data: <?php echo json_encode($totalExpenses); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Expense ($)'
                        }
                    }]
                },
                legend: {
                    display: false
                }
            }
        });
    </script>
</body>
</html>
