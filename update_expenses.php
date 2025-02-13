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

    // Process form data for updating expenses
    if (isset($_POST['update'])) {
        $selected_month = $_POST['selected_month'];
        $selected_year = $_POST['selected_year'];
        $monthly_salary = $_POST['monthly_salary'];
        $monthly_savings = $_POST['monthly_savings'];

        // Calculate total expenses from the expense table for the selected month and year
        $query_total_expenses = "SELECT SUM(expenseAmount) AS total_expenses FROM expense WHERE username = :username AND MONTH(date) = :month AND YEAR(date) = :year";
        $stmt_total_expenses = $conn->prepare($query_total_expenses);
        $stmt_total_expenses->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt_total_expenses->bindParam(':month', $selected_month, PDO::PARAM_INT);
        $stmt_total_expenses->bindParam(':year', $selected_year, PDO::PARAM_INT);
        $stmt_total_expenses->execute();
        $total_expenses_result = $stmt_total_expenses->fetch(PDO::FETCH_ASSOC);
        $total_expenses = $total_expenses_result['total_expenses'];

        // Calculate remaining amount
        $remaining_amount = $monthly_salary - $monthly_savings - $total_expenses;

        // Update the existing record with the new salary, savings, and saved amount
        $query_update_record = "UPDATE savings SET monthly_salary = :monthly_salary, expected_savings = :monthly_savings, saved_amount = :saved_amount WHERE username = :username AND month = :month AND year = :year";
        $stmt_update_record = $conn->prepare($query_update_record);
        $stmt_update_record->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt_update_record->bindParam(':monthly_salary', $monthly_salary, PDO::PARAM_INT);
        $stmt_update_record->bindParam(':monthly_savings', $monthly_savings, PDO::PARAM_INT);
        $stmt_update_record->bindParam(':saved_amount', $remaining_amount, PDO::PARAM_INT);
        $stmt_update_record->bindParam(':month', $selected_month, PDO::PARAM_INT);
        $stmt_update_record->bindParam(':year', $selected_year, PDO::PARAM_INT);
        $stmt_update_record->execute();
        // Redirect to the original expense calculation page or handle as needed
        header("Location: monthlycheckings.html");
        exit();
    } elseif (isset($_POST['no_update'])) {
        // User clicked "No", redirect to the original expense calculation page or handle as needed
        header("Location: monthlycheckings.html");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
