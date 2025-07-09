<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response to Inquiry</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/response.css">
</head>
<body>
    <section class="response">
        <p class="title">RESPONSE TO INQUIRY</p>
        <form action="" method="post">
            <label for="studentID">Student ID:</label>
            <input type="text" id="studentID" name="studentID" value="<?php echo $_GET['studentId'] ?? ''; ?>" required>
            <br><br>

            <label for="houseID">House ID:</label>
            <input type="text" id="houseID" name="houseID" value="<?php echo $_GET['houseId'] ?? ''; ?>" required>
            <br><br>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo $_GET['date'] ?? ''; ?>" required>
            <br><br>

            <label for="message">Message:</label>
            <br>
            <textarea name="message" id="message" readonly><?php echo htmlspecialchars(urldecode($_GET['message'] ?? '')); ?></textarea>
            <!--Before displaying the encoded message, it must be decoded so that's why urldecode() is used-->
            <br><br>

            <label for="response">Response:</label>
            <br>
            <textarea name="response" id="response" required></textarea>
            <br><br>

            <button type="submit">ENTER</button>
        </form>
    </section>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'config.php';

    $studentId = trim($_POST['studentID']);//trim() removes spaces from a string.
    $houseId = trim($_POST['houseID']);
    $inquiryDate = trim($_POST['date']);
    $responseMessage = trim($_POST['response']);

    
    $sql = "UPDATE inquiry 
            SET inquiryResponse = ?, inquiryStatus = 'Answered'
            WHERE studentId = ? AND houseId = ? AND inquiryDate = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $responseMessage, $studentId, $houseId, $inquiryDate);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        //The reason we need to check that the affected rows are more than sero is because, the execute() method will be true as long as the query runs successfully. 
        //If the update query finds no matches or tries to update a row that already has a response, it will still return true so we need to check if there are rows that have been affected.
        echo "<script>alert('Response submitted successfully.'); window.location.href='AllInquiries.php';</script>";
    } else {
        echo "<p style='color:red;'>Failed to update response. Check that the inquiry exists.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
</body>
</html>
