<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="icon" type="icon" href="hhw-images/hhw-favicon.png">
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost:3310";
    $username = "root";
    $password = ""; 
    $database = "househuntingwebsitedb";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $unhashedPassword = $_POST['password'];
    $hashedPassword = password_hash($unhashedPassword, PASSWORD_DEFAULT);
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    if (!$role || ($role !== 'student' && $role !== 'caretaker')) {
        echo "<script>alert('Please select a valid role.'); window.history.back();</script>";
        exit();
    }

    $studentCheck = $conn->prepare("SELECT 1 FROM student WHERE studentEmail = ?");
    $studentCheck->bind_param("s", $email);
    $studentCheck->execute();
    $studentCheck->store_result();
    $studentExists = $studentCheck->num_rows > 0;
    $studentCheck->close();

    $caretakerCheck = $conn->prepare("SELECT 1 FROM caretaker WHERE caretakerEmail = ?");
    $caretakerCheck->bind_param("s", $email);
    $caretakerCheck->execute();
    $caretakerCheck->store_result();
    $caretakerExists = $caretakerCheck->num_rows > 0;
    $caretakerCheck->close();

    if ($studentExists || $caretakerExists) {
        echo "<script>alert('This email is already in use. Please use a different one.'); window.history.back();</script>";
        exit();
    }

    try {
        if ($role === 'caretaker') {
            $sql = "INSERT INTO caretaker (caretakerName, caretakerEmail, caretakerPhoneNumber, caretakerPassword, isVerified, verificationDate)
                    VALUES (?, ?, ?, ?, false, NULL)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $phoneNumber, $hashedPassword);
        } else {
            $sql = "INSERT INTO student (studentName, studentEmail, studentPhoneNumber, studentPassword)
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $phoneNumber, $hashedPassword);
        }

        if (!$stmt->execute()) {
    $errorMsg = $stmt->error;
    if (strpos($errorMsg, "Duplicate entry.") !== false && strpos($errorMsg, "studentEmail") !== false) {
        echo "<script>alert('This email is already in use. Please try another.'); window.history.back();</script>";
    } else {
        echo "<script>alert('Signup failed. Please try again.'); window.history.back();</script>";
    }

        } else {
            if ($role === 'student') {
                header("Location: slanding.html");
            } else {
                header("Location: CaretakerLandingPage.php");
            }
            exit();
        }

    } catch (Exception $e) {
        echo "<script>alert('Error!!! " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>

</body>
</html>
