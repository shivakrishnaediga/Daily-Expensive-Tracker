<?php
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Get feedback text from the form
    $feedbackText = $_POST['feedback'];

    // Retrieve username from session
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        // Prepare SQL statement with parameterized query for security
        $sqlInsertFeedback = "INSERT INTO feedback (username, feedback_text, submission_date) VALUES (?, ?, NOW())";
        $stmtInsertFeedback = $conn->prepare($sqlInsertFeedback);

        if (!$stmtInsertFeedback) {
            die("Error in preparing SQL statement: " . $conn->error);
        }

        // Bind parameters to prevent SQL injection vulnerabilities
        $stmtInsertFeedback->bind_param("ss", $username, $feedbackText);

        if (!$stmtInsertFeedback->execute()) {
            die("Error executing query: " . $stmtInsertFeedback->error);
        }

        echo "Feedback submitted successfully.";
    } else {
        echo "Username not set in session.";
    }

    // Closing database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #e2e3f5;
        }

        .container {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 40px;
            width: 400px;
            max-width: 90%;
            margin-left: 450px;

        }

        h2 {
            text-align: center;
            color: #333333;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #555555;
        }

        textarea {
            padding: 12px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            resize: vertical;
            min-height: 120px;
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif;
        }

        button {
            background-color: #007bff;
            color: #ffffff;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Feedback Form</h2>
    <form method="post">
        <label for="feedback">Your Feedback:</label><br>
        <textarea id="feedback" name="feedback" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">Submit Feedback</button>
    </form>
    </div>

</body>
</html>