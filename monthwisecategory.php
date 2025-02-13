<?php
// Start the session
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

    // Check if a date is selected by the user
    $selected_year = isset($_POST['selected_year']) ? $_POST['selected_year'] : date("Y");

    // Fetch expenses for the user and selected year
    $start_date = $selected_year . "-01-01"; // First day of selected year
    $end_date = $selected_year . "-12-31";   // Last day of selected year
    $query_expenses = "SELECT category, SUM(expenseAmount) as total_expense FROM expense WHERE username=:username AND date BETWEEN :start_date AND :end_date GROUP BY expenseName";
    $stmt_expenses = $conn->prepare($query_expenses);
    $stmt_expenses->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
    $stmt_expenses->bindParam(':start_date', $start_date, PDO::PARAM_STR);
    $stmt_expenses->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    $stmt_expenses->execute();
    $expenses = $stmt_expenses->fetchAll(PDO::FETCH_ASSOC);

    // Extracting data for chart
    $categories = array();
    $totals = array();
    foreach ($expenses as $expense) {
        $categories[] = $expense['category'];
        $totals[] = $expense['total_expense'];
    }

    // Convert data to JSON format for Chart.js
    $categories_json = json_encode($categories);
    $totals_json = json_encode($totals);

    // Check if there are no expenses for the selected year
    if (empty($expenses)) {
        $no_expense_message = "No expenses found for the year $selected_year.";
    } else {
        $no_expense_message = "";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category-wise Expenses Pie Chart</title>
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
        }
        #pie{
            width:40%;
        }
        h2{
            text-align:center;
        }
        input[type="submit"] {
            height: 30px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            margin-left:20px;
        }
    </style>
</head>
<body>
<center> 
<div id="con">
    <!-- Form to select year -->
    <h2>Yealry expenses</h2>
    <form method="POST">
       <label for="selected_year">Select Year:</label>
        <input type="number" id="selected_year" name="selected_year" value="<?php echo isset($selected_year) ? $selected_year : ''; ?>" min="1900" max="2099" step="1" />
        <input type="submit" value="Show Expenses">
    </form>

    <!-- Canvas for the pie chart -->
    <div id="pie">
    <canvas id="expenseChart"></canvas>
    </div>
</div>
    </center>
    <!-- Display message if there are no expenses for the selected year -->
    <?php if (!empty($no_expense_message)) : ?>
        <p><?php echo $no_expense_message; ?></p>
    <?php endif; ?>
</div>

<script>
    // Data for the chart
    var categories = <?php echo $categories_json; ?>;
    var totals = <?php echo $totals_json; ?>;

    // Creating pie chart
    var ctx = document.getElementById('expenseChart').getContext('2d');
    var expenseChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: categories,
            datasets: [{
                label: 'Category-wise Expenses',
                data: totals,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Category-wise Expenses'
            }
        }
    });
</script>
</body>
</html>
