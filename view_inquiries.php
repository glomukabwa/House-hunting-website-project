<?php
session_start();
include 'config.php';

// Check if the student is logged in
if (!isset($_SESSION['studentId'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit;
}

$studentId = $_SESSION['studentId'];

$query = "
    SELECT i.inquiryId, h.houseTitle, h.houseLocation, i.inquiryMessage, i.inquiryResponse, i.inquiryDate
    FROM Inquiry i
    JOIN House h ON i.houseId = h.houseId
    WHERE i.studentId = ?
    ORDER BY i.inquiryDate DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Inquiries</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/view_inquiries.css">
    
</head>
<body>
<br><br><br>
<h1>Your Inquiries</h1>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>House Title</th>
            <th>Location</th>
            <th>Your Message</th>
            <th>Response</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['houseTitle']) ?></td>
                <td><?= htmlspecialchars($row['houseLocation']) ?></td>
                <td><?= htmlspecialchars($row['inquiryMessage']) ?></td>
                <td><?= $row['inquiryResponse'] ? htmlspecialchars($row['inquiryResponse']) : 'No response yet' ?></td>
                <td><?= htmlspecialchars($row['inquiryDate']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center;">You have not made any inquiries yet.</p>
<?php endif; ?>

<br><br>
<a href="StudentLanding.php" class="back-btn">Back to Home</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
