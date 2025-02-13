<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
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

// Query to retrieve expenses data based on username
$sql = "SELECT date, SUM(expenseAmount) AS total_expense FROM expense WHERE username = ? GROUP BY date ORDER BY date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Initialize arrays to store dates and total expenses
$dates = [];
$totalExpenses = [];

if ($result->num_rows > 0) {
    // Fetching data and populating arrays
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row["date"];
        $totalExpenses[] = $row["total_expense"];
    }
} else {
    echo "No expenses data found.";
}

$conn->close();
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
<h2>Daily Expenses</h2>
    <div id="con">
    <div>
        <canvas id="expensesChart"></canvas>
    </div>
    </div>

<script>
    // JavaScript code to create the chart
    var ctx = document.getElementById('expensesChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [{
                label: 'Expenses Over Time',
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
                xAxes: [{
                    type: 'time',
                    time: {
                        unit: 'day',
                        displayFormats: {
                            day: 'MMM DD'
                        }
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Date'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Expense ($)'
                    }
                }]
            },
            legend: {
                display: true,
                position: 'top'
            }
        }
    });
</script>
</body>
</html>
