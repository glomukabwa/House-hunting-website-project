<?php
include 'config.php';

// Fetch unanswered inquiries
$sqlPending = "SELECT * FROM inquiry WHERE inquiryStatus = 'Pending...'";
$resultPending = $conn->query($sqlPending);

// Fetch answered inquiries
$sqlAnswered = "SELECT * FROM inquiry WHERE inquiryStatus != 'Pending...'";
$resultAnswered = $conn->query($sqlAnswered);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Inquiries</title>
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
            <a href="#">SIGN UP</a>
            <a href="#">LOG IN</a>
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
     </main>
     <footer>
        <div class="sitemap">
            <a href="#">HOME</a>
            <a href="#">ABOUT US</a>
            <a href="#">SIGN UP</a>
            <a href="#">LOG IN</a>
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
