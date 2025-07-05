<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="stylesheet1.css">
    <script src="script1.js"></script>
</head>
<body class="signup-body">
    <form class="form-container" method="post" action="">

        <h1 class="signup-title">SIGN UP</h1>

        <label for="name">Enter Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>
        <label for="email">Enter Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
        <label for="phoneNumber">Enter Phone Number:</label><br>
            <input type="text" id="phoneNumber" name="phoneNumber" maxlength="10" pattern="\d{10}" required><br><br>
        <label for="password">Enter Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
        <label for="role">I am a:</label><br>
            <input type="radio" name="role" value="student" required> Student<br>
            <input type="radio" name="role" value="caretaker" required> Caretaker<br><br>
        <div class="button-container">
            <input type="submit" value="ENTER" class="signup-button">
        </div>
    </form>

    <?php
    include 'config.php';
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            /*$servername = "localhost:3310";
            $username = "root";
            $password = ""; 
            $database = "househuntingwebsitedb";

            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }*/

            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phoneNumber = trim($_POST['phoneNumber']);
            $unhashedPassword = $_POST['password'];
            $hashedPassword = password_hash($unhashedPassword, PASSWORD_DEFAULT);
            $role = $_POST['role']; 

            if ($role === 'caretaker') {
                $sql = "INSERT INTO caretaker (caretakerName, caretakerEmail, caretakerPhoneNumber, caretakerPassword, isVerified, verificationDate)
                        VALUES (?, ?, ?, ?, false, NULL)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $email, $phoneNumber, $hashedPassword);
            } elseif ($role === 'student') {
                $sql = "INSERT INTO student (studentName, studentEmail, studentPhoneNumber, studentPassword)
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $email, $phoneNumber, $hashedPassword);
            } 

            if ($stmt->execute()) {
                if ($role === 'student') {
                    header("Location: StudentLanding.php");
                } elseif ($role === 'caretaker') {
                    header("Location: CaretakerLandingPage.php");
                } 
                exit();
            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
            $conn->close();
        }
    ?>
</body>
</html>