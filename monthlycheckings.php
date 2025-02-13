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

    // Process form data
    $selected_month = $_POST['selected_month'];
    $selected_year = $_POST['selected_year'];
    $monthly_salary = $_POST['monthly_salary'];
    $monthly_savings = $_POST['monthly_savings'];

    // Calculate total expenses from the database for the selected month and year
    $query_total_expenses = "SELECT SUM(expenseAmount) AS total_expenses FROM expense WHERE username = :username AND YEAR(date) = :year AND MONTH(date) = :month";
    $stmt_total_expenses = $conn->prepare($query_total_expenses);
    $stmt_total_expenses->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt_total_expenses->bindParam(':month', $selected_month, PDO::PARAM_INT);
    $stmt_total_expenses->bindParam(':year', $selected_year, PDO::PARAM_INT);
    $stmt_total_expenses->execute();
    $total_expenses_result = $stmt_total_expenses->fetch(PDO::FETCH_ASSOC);
    $total_expenses_from_db = $total_expenses_result['total_expenses'];

    // Check if a record already exists for the selected month and year
    $query_existing_record = "SELECT * FROM savings WHERE username = :username AND month = :month AND year = :year";
    $stmt_existing_record = $conn->prepare($query_existing_record);
    $stmt_existing_record->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt_existing_record->bindParam(':month', $selected_month, PDO::PARAM_INT);
    $stmt_existing_record->bindParam(':year', $selected_year, PDO::PARAM_INT);
    $stmt_existing_record->execute();
    $existing_record = $stmt_existing_record->fetch(PDO::FETCH_ASSOC);

    if ($existing_record) {
        // Inform the user that the record already exists
        echo "<p>A record already exists for the selected month and year.</p>";
        $savings_exceed_salary = $monthly_savings > $monthly_salary;
        // Inform user if savings exceed salary
        if ($savings_exceed_salary) {
            echo "<p>Your expected savings exceed your monthly salary.</p>";
        } else {
            echo "<p>Do you want to update your salary and expected savings?</p>";
            echo "<form method='POST' action='update_expenses.php'>";
            echo "<input type='hidden' name='selected_month' value='$selected_month'>";
            echo "<input type='hidden' name='selected_year' value='$selected_year'>";
            echo "<input type='hidden' name='monthly_salary' value='$monthly_salary'>";
            echo "<input type='hidden' name='monthly_savings' value='$monthly_savings'>";
            echo "<input type='submit' name='update' value='Yes'>";
            echo "</form>";

            echo "<form action='notupdate.php' method='POST'>";
            echo "<input type='submit' name='no_update' value='No'>";
            echo "</form>";
        }
    } else {
        // Proceed with calculating expenses and inserting data

        // Check if expected savings exceed monthly salary
        $savings_exceed_salary = $monthly_savings > $monthly_salary;
        // Inform user if savings exceed salary
        if ($savings_exceed_salary) {
            echo "<p>Your expected savings exceed your monthly salary.</p>";
        } else {
            // Display breakdown of expenses
            echo "<h2>Breakdown of Expenses:</h2>";
            echo "<p>Total expenses for $selected_month/$selected_year: $total_expenses_from_db</p>";

            // Calculate remaining amount
            $total_expenses = $monthly_salary - $monthly_savings + $total_expenses_from_db;
            $remaining_amount = $monthly_salary - $total_expenses_from_db;

            // Display remaining amount
            echo "<h2>Remaining Amount:</h2>";
            echo "<p>Your remaining amount after deducting expenses: $remaining_amount</p>";

            // Check if remaining amount can reach savings goal
            $can_reach_goal = $remaining_amount >= $monthly_savings;

            // Display message about savings goal
            if ($can_reach_goal) {
                echo "<p>You can reach your savings goal with the current expenses.</p>";
            } else {
                echo "<p>You cannot reach your savings goal with the current expenses.</p>";
            }

            // Insert data into the new table
            $query_insert_data = "INSERT INTO savings (username, monthly_salary, expected_savings, saved_amount, month, year) VALUES (:username, :salary, :expected_savings, :saved_amount, :month, :year)";
            $stmt_insert_data = $conn->prepare($query_insert_data);
            $stmt_insert_data->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt_insert_data->bindParam(':salary', $monthly_salary, PDO::PARAM_INT);
            $stmt_insert_data->bindParam(':expected_savings', $monthly_savings, PDO::PARAM_INT);
            $stmt_insert_data->bindParam(':saved_amount', $remaining_amount, PDO::PARAM_INT);
            $stmt_insert_data->bindParam(':month', $selected_month, PDO::PARAM_INT);
            $stmt_insert_data->bindParam(':year', $selected_year, PDO::PARAM_INT);
            $stmt_insert_data->execute();
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
