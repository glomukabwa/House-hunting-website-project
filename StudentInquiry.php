<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Inquiry Page</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="stylesheet1.css">
    <script src="script1.js"></script>
</head>
<body class="studentInquiry-body">
    <form id="inquiryForm" class="studentInquiry-form-container" method="post" action="">

        <h1 class="studentInquiry-title">INQUIRY</h1>
    
        <label for="studentId">Student ID:</label><br>
        <input type="text" id="studentId" name="studentId" required><br><br>
        <label for="houseId">House ID:</label><br>
        <input type="text" id="houseId" name="houseId" required><br><br>
        <label for="date">Date:</label><br>
        <input type="date" id="date" name="date" required><br><br>
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="8" cols="42" required class="studentInquiry-message" placeholder="Enter message..."></textarea><br><br>
        
        <div class="button-container">
            <input type="submit" value="ENTER" class="studentInquiry-button">
        </div>
    </form>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //$servername = "localhost";
            //$username = "root";
            //$password = ""; 
            //$database = "househuntingwebsitedb";

            //$conn = new mysqli($servername, $username, $password, $database);

            //if ($conn->connect_error) {
                //die("Connection failed: " . $conn->connect_error);
            //}
            require 'config.php';

            $studentId = trim($_POST['studentId']);
            $houseId = trim($_POST['houseId']);
            $date = trim($_POST['date']);
            $message = htmlspecialchars(trim($_POST['message']));

            $sql = "INSERT INTO inquiry (studentId, houseId, inquiryDate, inquiryMessage, inquiryStatus)
                    VALUES (?, ?, ?, ?, 'Pending...')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $studentId, $houseId, $date, $message);

            if ($stmt->execute()) {
                echo "<p class='confirmation' style='color: green; font-weight: bold;'>Inquiry submitted successfully! Redirecting...</p>";

        
                echo "<script>
                        document.getElementById('inquiryForm').style.display = 'none';
                        setTimeout(function() {
                            window.location.href = 'StudentLanding.php';
                        }, 2000);
                    </script>";
            } else {
                echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
            $conn->close();
        }
    ?>
</body>
</html>