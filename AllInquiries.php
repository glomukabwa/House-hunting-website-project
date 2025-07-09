<?php
session_start();
require 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['caretakerId'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$caretakerId = $_SESSION['caretakerId'];

// Fetch unanswered inquiries for this caretaker's houses
$sqlPending = "SELECT i.* 
               FROM inquiry i
               JOIN house h ON i.houseId = h.houseId
               WHERE h.caretakerId = ? AND i.inquiryStatus = 'Pending...'";
$stmtPending = $conn->prepare($sqlPending);
$stmtPending->bind_param("i", $caretakerId);
$stmtPending->execute();
$resultPending = $stmtPending->get_result();

// Fetch answered inquiries for this caretaker's houses
$sqlAnswered = "SELECT i.* 
                FROM inquiry i
                JOIN house h ON i.houseId = h.houseId
                WHERE h.caretakerId = ? AND i.inquiryStatus != 'Pending...'";
$stmtAnswered = $conn->prepare($sqlAnswered);
$stmtAnswered->bind_param("i", $caretakerId);
$stmtAnswered->execute();
$resultAnswered = $stmtAnswered->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Caretaker Landing Page</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/allinquiries.css">
</head>
<body>
    <div class="wrapper">
    <header>
        <div class="logo">
            <img src="hhw-images/hhw-logo.png" alt="Logo" class="logo-img">
        </div>
        <nav>
            <a href="#">HOME</a>
            <a href="#">ABOUT US</a>
            <a href="SignupPage.php">SIGN UP</a>
            <a href="Login.php">LOG IN</a>
        </nav>
        <div class="profile">
            <img src="images/black.jpeg" alt="black">
        </div>
     </header>
     <main>
<section class="response">
    <h1>UNANSWERED INQUIRIES</h1>
    <?php if ($resultPending && $resultPending->num_rows > 0): ?>
        <table>
            <tr>
                <th>Student ID</th>
                <th>House ID</th>
                <th>Date</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $resultPending->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['studentId']) ?></td>
                    <td><?= htmlspecialchars($row['houseId']) ?></td>
                    <td><?= htmlspecialchars($row['inquiryDate']) ?></td>
                    <td><?= htmlspecialchars($row['inquiryMessage']) ?></td>
                    <td><?= htmlspecialchars($row['inquiryStatus']) ?></td>
                    <td>
                        <?php
                        $message = urlencode($row['inquiryMessage']);
                        // We use the above because if a message contains spaces, punctuation, or line breaks, these characters break the URL unless they are properly encoded.
                        // urlencode() makes sure that any special characters (like spaces, quotes, symbols) are safely passed in the URL
                        // Special characters like & are seen as a new parameter in the URL, so we need to encode them
                        // Encoding also ensures safe transportation of the message through the URL

                        $link = "response.php?studentId={$row['studentId']}&houseId={$row['houseId']}&date={$row['inquiryDate']}&message={$message}";
                        ?>
                        <a href="<?= $link ?>"><button>Respond</button></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending inquiries.</p>
    <?php endif; ?>
</section>

<br><br><br><br><br><br>

<section class="response">
    <h1>ANSWERED INQUIRIES</h1>
    <?php if ($resultAnswered && $resultAnswered->num_rows > 0): ?>
        <table>
            <tr>
                <th>Student ID</th>
                <th>House ID</th>
                <th>Date</th>
                <th>Message</th>
                <th>Status</th>
                <th>Response</th>
            </tr>
            <?php while ($row = $resultAnswered->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['studentId']) ?></td>
                    <td><?= htmlspecialchars($row['houseId']) ?></td>
                    <td><?= htmlspecialchars($row['inquiryDate']) ?></td>
                    <td><?= htmlspecialchars($row['inquiryMessage']) ?></td>
                    <td><?= htmlspecialchars($row['inquiryStatus']) ?></td>
                    <td><?= htmlspecialchars($row['inquiryResponse']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No answered inquiries.</p>
    <?php endif; ?>
</section>
<br><br><br><br>
 <div class="add-house-btn-wrapper">
    <form action="houselisting.php" method="get">
      <input type="submit" value="ADD HOUSE LISTING" class="caretakerLanding-button" />
    </form>
  </div>
     </main>
     <footer>
        <div class="sitemap">
            <a href="#">HOME</a>
            <a href="#">ABOUT US</a>
            <a href="SignupPage.php">SIGN UP</a>
            <a href="Login.php">LOG IN</a>
        </div>
        <div class="contacts">
            <p>GET IN TOUCH WITH US</p>
            <p>webhunt@gmail.com</p>
            <p>+254178653987</p>
            <p>Nairobi,Kenya</p>
        </div>
     </footer>
</div>
</body>
</html>
