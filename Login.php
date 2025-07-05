<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="stylesheet1.css">
    <script src="script1.js"></script>
</head>
<body class="login-body">
    <form class="form-container" action="" method="POST">

        <h1 class="login-title">LOGIN</h1>
        
        <label for="email">Enter Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
        <label for="password">Enter Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
        <div class="button-container">
            <input type="submit" value="ENTER" class="login-button">
        </div>
    </form>
    
    <?php
        session_start();
        include 'config.php'; 

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            //$servername = "localhost:3301";
            //$username = "root";
            //$password = ""; 
            //$database = "househuntingwebsitedb"; 

            //$conn = new mysqli($servername, $username, $password, $database);
            
            //if ($conn->connect_error) {
                //die("Connection failed: " . $conn->connect_error);
            //}

            $email = trim($_POST['email']);
            $inputPassword = $_POST['password'];

            $stmt = $conn->prepare("SELECT * FROM caretaker WHERE caretakerEmail = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($inputPassword, $row['caretakerPassword'])) {
                    $_SESSION['caretakerId'] = $row['caretakerId'];
                    header("Location: CaretakerLandingPage.php");
                    exit();
                }
            }
            $stmt->close();

            $stmt = $conn->prepare("SELECT * FROM admin WHERE adminEmail = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($inputPassword, $row['adminPassword'])) {
                    $_SESSION['adminId'] = $row['adminId'];
                    header("Location: admin.php");
                    exit();
                }
            }
            $stmt->close();

            $stmt = $conn->prepare("SELECT * FROM student WHERE studentEmail = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($inputPassword, $row['studentPassword'])) {
                    $_SESSION['studentId'] = $row['studentId'];
                    header("Location: StudentLandingPage.html");
                    exit();
                }
            }
            $stmt->close();
            $conn->close();
            echo "<p style='color:red; text-align:center;'>Invalid login. Please try again.</p>";
        }
    ?>
</body>
</html>
