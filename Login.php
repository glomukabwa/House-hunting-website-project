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
        <label for="role">I am a/an:</label><br>
            <input type="radio" name="role" value="student" required> Student<br>
            <input type="radio" name="role" value="caretaker" required> Caretaker<br>
            <input type="radio" name="role" value="admin" required> Admin<br><br>
        <div class="button-container">
            <input type="submit" value="ENTER" class="login-button">
        </div>
    </form>
    
    <?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //require 'config.php'; // Include your database connection file
    $servername = "localhost:3306";
    $username = "root";
    $password = ""; 
    $database = "househuntingwebsitedb"; 

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = trim($_POST['email']);
    $inputPassword = $_POST['password'];
    $role = $_POST['role'];

    // Check existence across all roles
    $studentStmt = $conn->prepare("SELECT studentPassword, studentId, studentName FROM student WHERE studentEmail = ?");
    $studentStmt->bind_param("s", $email);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();
    $student = $studentResult->fetch_assoc();
    $studentStmt->close();

    $caretakerStmt = $conn->prepare("SELECT caretakerPassword, caretakerId, caretakerName FROM caretaker WHERE caretakerEmail = ?");
    $caretakerStmt->bind_param("s", $email);
    $caretakerStmt->execute();
    $caretakerResult = $caretakerStmt->get_result();
    $caretaker = $caretakerResult->fetch_assoc();
    $caretakerStmt->close();

    $adminStmt = $conn->prepare("SELECT adminPassword, adminId, adminName FROM admin WHERE adminEmail = ?");
    $adminStmt->bind_param("s", $email);
    $adminStmt->execute();
    $adminResult = $adminStmt->get_result();
    $admin = $adminResult->fetch_assoc();
    $adminStmt->close();

    // Email doesn't exist at all
    if (!$student && !$caretaker && !$admin) {
        echo "<script>alert('Invalid email. Please sign up.'); window.history.back();</script>";
    }
    // Email exists, but in a different role
    elseif (($role === 'student' && !$student) || ($role === 'caretaker' && !$caretaker) || ($role === 'admin' && !$admin)) {
        echo "<script>alert('Please select the correct role.'); window.history.back();</script>";
    }
    else {
        // Correct role → verify password

        if ($role === 'student') {
            if (password_verify($inputPassword, $student['studentPassword'])) {
                $_SESSION['studentId'] = $student['studentId'];
                echo "<script>alert('Welcome back " . addslashes($student['studentName']) . "'); window.location.href='StudentLanding.php';</script>";
                exit();
            } else {
                echo "<script>alert('Incorrect password!!!'); window.history.back();</script>";
            }
        } elseif ($role === 'caretaker') {
            if (password_verify($inputPassword, $caretaker['caretakerPassword'])) {
                $_SESSION['caretakerId'] = $caretaker['caretakerId'];
                echo "<script>alert('Welcome back " . addslashes($caretaker['caretakerName']) . "'); window.location.href='CaretakerLandingPage.php';</script>";
                exit();
            } else {
                echo "<script>alert('Incorrect password!!!'); window.history.back();</script>";
            }
        } elseif ($role === 'admin') {
            if (password_verify($inputPassword, $admin['adminPassword'])) {
                $_SESSION['adminId'] = $admin['adminId'];
                echo "<script>alert('Welcome back " . addslashes($admin['adminName']) . "'); window.location.href='admin.html';</script>";
                exit();
            } else {
               echo "<script>alert('Incorrect password!!!'); window.history.back();</script>";
            }
        }
    }

    $conn->close();
}
?>

</body>
</html>
