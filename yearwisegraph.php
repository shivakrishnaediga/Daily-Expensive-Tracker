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
<h2>Yearly Expenses</h2>
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

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve user ID based on username
        $query_expenses = "SELECT YEAR(e.date) AS year, SUM(e.expenseAmount) AS total_expense 
                           FROM expense e
                           INNER JOIN signup s ON e.username = s.username
                           WHERE s.username = :username
                           GROUP BY YEAR(e.date)
                           ORDER BY YEAR(e.date)";
        $stmt_expenses = $conn->prepare($query_expenses);
        $stmt_expenses->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
        $stmt_expenses->execute();
        $expenses_data = $stmt_expenses->fetchAll(PDO::FETCH_ASSOC);

        // Initialize arrays to store years and total expenses
        $years = [];
        $totalExpenses = [];

        foreach ($expenses_data as $expense) {
            $years[] = $expense["year"];
            $totalExpenses[] = $expense["total_expense"];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>

    <script>
        // JavaScript code to create the chart
        var ctx = document.getElementById('expensesChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($years); ?>,
                datasets: [{
                    label: 'Expenses Per Year',
                    data: <?php echo json_encode($totalExpenses); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Expense ($)'
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Year'
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
