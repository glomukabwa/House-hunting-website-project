<?php
session_start();
require 'config.php';
/*Difference btwn require and include:
  If the file(config.php) is missing or has an error for require, php stops the entire script and throws a fatal error. This is good when a file is essential to the application
  If the file is missing or has an error for include, php throws a warning but the script continues running. This can lead to errors later and is used for optional content like headers, footers etc (I know I've used it in other files but that's cz I know config.php works) */

// Restricting access to caretakers that have logged in only
if (!isset($_SESSION['caretakerId'])) {
    echo "<script>alert('Please log in as a caretaker first.'); window.location.href='login.php';</script>";
    exit();
}

$caretakerId = $_SESSION['caretakerId'];

// Checking if the caretaker is verified by the admin before granting access to the landing page
$sql = $conn->prepare("SELECT isVerified FROM caretaker WHERE caretakerId = ?");
$sql->bind_param("i", $caretakerId);
$sql->execute();
$sql->bind_result($isVerified);
$sql->fetch();
$sql->close();

if (!$isVerified) {
    echo "<script>alert('Your account has not been verified by the admin.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch inquiries for this caretaker’s houses
$query = "
SELECT i.* FROM inquiry i
JOIN house h ON i.houseId = h.houseId
WHERE h.caretakerId = ?
ORDER BY i.inquiryDate DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $caretakerId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Caretaker Landing Page</title>
  <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
  <link rel="icon" href="hhw-images/hhw-favicon.png" />
  <link rel="stylesheet" href="stylesheet1.css" />
</head>

<body class="caretakerLanding-body">
  <header>
    <div class="logo-link">
        <a href="StudentSearchResultsPage.html">
            <img src="hhw-images/hhw-logo.png" alt="Logo" class="logo-img">
        </a>
    </div>

    <div class="topnav">
      <a href="CaretakerProfilePage.html">PROFILE</a>
      <a href="#news">News</a>
      <a href="#contact">Contact</a>
      <a href="#about">About</a>
      <a class="active" href="CaretakerLandingPage.php">HOME</a>
    </div>
  </header>

  <h2 class="clp-h2">INQUIRIES:</h2>

  <table>
    <thead>
      <tr>
        <th>Inquiry ID</th>
        <th>Student ID</th>
        <th>House ID</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['inquiryId'] ?></td>
          <td><?= $row['studentId'] ?></td>
          <td><?= $row['houseId'] ?></td>
          <td><?= $row['inquiryDate'] ?></td>
          <td><?= $row['inquiryStatus'] ?></td>
          <td>
            <form method="GET" action="viewInquiry.php">
              <input type="hidden" name="id" value="<?= $row['inquiryId'] ?>" />
              <button class="view-btn">VIEW</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="add-house-btn-wrapper">
    <form action="houselisting.php" method="get">
      <input type="submit" value="ADD HOUSE LISTING" class="caretakerLanding-button" />
    </form>
  </div>

  <footer>
    <div class="footer">
      FOOTER
    </div>
  </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
