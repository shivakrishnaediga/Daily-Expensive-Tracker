<?php
session_start();
function open_connection() {
    $host = 'localhost';
    $db_username = 'root';
    $db_password = "";
    $database = "welcome";
    $conn = new mysqli($host, $db_username, $db_password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = open_connection();
    $username = $_SESSION['username'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $occupation = $_POST['occupation'];
    $address = $_POST['address'];

    // Insert new profile
    $sql_insert = "INSERT INTO profile (username, email, firstname, lastname, age, gender, occupation, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssssss", $username, $email, $firstname, $lastname, $age, $gender, $occupation, $address);
    $profile_insert_result = $stmt_insert->execute();

    if ($profile_insert_result) {
        echo "Profile setup successfully.";
    } else {
        echo "Error setting up profile: " . $conn->error;
    }
    $stmt_insert->close();
    $conn->close();
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Setup</title>
    <style>

  body {
    font-family: Arial, sans-serif;
    background: url('https://static.vecteezy.com/system/resources/thumbnails/003/564/850/small/modern-abstract-dynamic-stripes-gradient-violet-background-design-free-vector.jpg') top/cover no-repeat;
    background-size: 100% 50%, cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
#avatar {
  width: 90px;
  height: 90px;
  border: 3px solid #7260eb; 
  border-radius: 50%; 
  overflow: hidden; 
  margin-left: 130px; 
  margin-bottom: 20px;
 
}

#avatar img {
  width: 100%; 
  height: 100%;
  object-fit: cover;
}
  .profile-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 350px;
    padding: 15px;
    text-align: center;
  }
  input{
            padding: 5px;
            width: 60%;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"]{
            background-color: #911faa;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button{
            background-color: #911faa;
            color: #ffffff;
            border: none;
            padding: 5px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top:5px;
            margin-left:20px;
        }
</style>
</head>
<body>
    
<div class="profile-card">
  <div id="avatar"></div>
    <form action="" method="post">
        
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="age">Age:</label>
        <input type="tel" id="age" name="age"><br><br>
        
        <label for="gender">Gender:</label>
        <input type="text" id="gender" name="gender"><br><br>
        
        <label for="occupation">Occupation:</label>
        <input type="text" id="occupation" name="occupation"><br><br>
        
        <label for="address">Address:</label>
        <input type="text" id="address" name="address"><br><br>

        <input type="submit" value="Setup Profile">
    </form>
    <button onclick="window.location.href = 'updateprofile.html';">Any changes</button>

    </div>

    <script>
  var randomString = Math.random().toString(36).substring(7);
    var robohashURL = "https://robohash.org/" + randomString + ".png";
    var imgElement = document.createElement("img");
    imgElement.src = robohashURL;
    imgElement.alt = "User Avatar";
    document.getElementById("avatar").appendChild(imgElement);
</script>

</body>
</html>
<?php
}
?>
