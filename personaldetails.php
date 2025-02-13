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
    width: 400px;
    margin: 0 auto;
    border-radius: 5px;
}

input[type="checkbox"] {
    margin-right: 10px;
}

label {
    font-weight: bold;
}

input[type="text"],input[type="email"],input[type="tel"] {
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
    $firstname = $lastname = $email = $age = $gender = $occupation = $address = "";
    $username = $_SESSION['username'];
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update First Name
        if (isset($_POST['update_firstname']) && !empty($_POST['firstname'])) {
            $firstname = test_input($_POST['firstname']);
            $sql = "UPDATE profile SET firstname='$firstname' WHERE username = '$username'";
            $conn->query($sql);
        }

        // Update Last Name
        if (isset($_POST['update_lastname']) && !empty($_POST['lastname'])) {
            $lastname = test_input($_POST['lastname']);
            $sql = "UPDATE profile SET lastname='$lastname' WHERE username = '$username'";
            $conn->query($sql);
        }

        // Update Email
        if (isset($_POST['update_email']) && !empty($_POST['email'])) {
            $email = test_input($_POST['email']);
            $sql = "UPDATE profile SET email='$email' WHERE username = '$username'";
            $conn->query($sql);
        }

        // Update Age
        if (isset($_POST['update_age']) && !empty($_POST['age'])) {
            $age = test_input($_POST['age']);
            $sql = "UPDATE profile SET age='$age' WHERE username = '$username'";
            $conn->query($sql);
        }

        // Update Gender
        if (isset($_POST['update_gender']) && !empty($_POST['gender'])) {
            $gender = test_input($_POST['gender']);
            $sql = "UPDATE profile SET gender='$gender' WHERE username = '$username'";
            $conn->query($sql);
        }

        // Update Occupation
        if (isset($_POST['update_occupation']) && !empty($_POST['occupation'])) {
            $occupation = test_input($_POST['occupation']);
            $sql = "UPDATE profile SET occupation='$occupation' WHERE username = '$username'";
            $conn->query($sql);
        }

        // Update Address
        if (isset($_POST['update_address']) && !empty($_POST['address'])) {
            $address = test_input($_POST['address']);
            $sql = "UPDATE profile SET address='$address' WHERE username = '$username'";
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
        <input type="checkbox" id="update_firstname" name="update_firstname" value="1">
        <label for="update_firstname">Update First Name:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>"><br><br>

        <input type="checkbox" id="update_lastname" name="update_lastname" value="1">
        <label for="update_lastname">Update Last Name:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>"><br><br>

        <input type="checkbox" id="update_email" name="update_email" value="1">
        <label for="update_email">Update Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>"><br><br>

        <input type="checkbox" id="update_age" name="update_age" value="1">
        <label for="update_age">Update Age:</label>
        <input type="tel" id="age" name="age" value="<?php echo $age; ?>"><br><br>

        <input type="checkbox" id="update_gender" name="update_gender" value="1">
        <label for="update_gender">Update Gender:</label>
        <input type="text" id="gender" name="gender" value="<?php echo $gender; ?>"><br><br>

        <input type="checkbox" id="update_occupation" name="update_occupation" value="1">
        <label for="update_occupation">Update Occupation:</label>
        <input type="text" id="occupation" name="occupation" value="<?php echo $occupation; ?>"><br><br>

        <input type="checkbox" id="update_address" name="update_address" value="1">
        <label for="update_address">Update Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>"><br><br>

        <input type="submit" value="Update Profile">
    </form>
</body>
</html>
