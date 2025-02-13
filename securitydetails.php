<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body{
            background-color: #8ce4ff;
        }
        form {
    background-color: #f4f4f4;
    padding: 20px;
    width: 300px;
    margin: 0 auto;
    border-radius: 5px;
}

input[type="checkbox"] {
    margin-right: 10px;
}

label {
    font-weight: bold;
}

input[type="text"] {
    padding: 5px;
    margin-bottom: 10px;
    width: 100%;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #f556a5;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #f72d91;
}
</style>
</head>
<body>
    <h2><center>Edit Profile</center></h2>
    <?php
    // Database connection details
    session_start();
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

    // Define variables and set them to empty values
    $phonenumber =  $password = "";
    $username = $_SESSION['username'];
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update First Name
        if (isset($_POST['update_phonenumber']) && !empty($_POST['phonenumber'])) {
            $phonenumber = test_input($_POST['phonenumber']);
            $sql = "UPDATE signup SET phonenumber='$phonenumber' WHERE username = '$username'";
            $conn->query($sql);
        }

        // Update Last Name
        if (isset($_POST['update_password']) && !empty($_POST['password'])) {
            $password = test_input($_POST['password']);
            $sql = "UPDATE signup SET password='$password' WHERE username = '$username'";
            $conn->query($sql);
        }
        
    }

    // Close the database connection
    $conn->close();

    // Function to sanitize input data
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>
    <form action="" method="post">
        <input type="checkbox" id="update_phonenumber" name="update_phonenumber" value="1">
        <label for="update_phonenumber">Update Phone number:</label>
        <input type="text" id="phonenumber" name="phonenumber" value="<?php echo $phonenumber; ?>"><br><br>

        <input type="checkbox" id="update_password" name="update_password" value="1">
        <label for="update_password">Update Password:</label>
        <input type="text" id="password" name="password" value="<?php echo $password; ?>"><br><br>

        <input type="submit" value="Update Details">
    </form>
</body>
</html>
