<?php
$username = filter_input(INPUT_POST, 'username');
$phonenumber = filter_input(INPUT_POST, 'phonenumber');
$password = filter_input(INPUT_POST, 'password');

// Ensure all fields are not empty
if (!empty($username) && !empty($phonenumber) && !empty($password)) {
    // Validate phone number format (example: 9876543210)
    if (preg_match('/^\d{10}$/', $phonenumber)) {
        $host = "localhost";
        $dbusername = "root";
        $dbpassword = "";
        $dbname = "welcome";

        $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
        if (mysqli_connect_error()) {
            die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        } else {
            // Check if the username already exists in the database
            $sql_check = "SELECT username FROM signup WHERE username = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            if ($result_check->num_rows > 0) {
                // If the username already exists, display an error message
                echo "Username already exists. Please choose a different username.";
            } else {
                // Insert the record with the provided username
                $sql = "INSERT INTO signup (username, phonenumber, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $username, $phonenumber, $password);
                if ($stmt->execute()) {
                    echo 'New record inserted with username: ' . $username;
                } else {
                    echo 'Error: ' . $sql . '<br>' . $conn->error;
                }
            }
            $conn->close();
        }
    } else {
        echo "Invalid phone number format";
    }
} else {
    echo "One or more fields are empty";
}
?>
