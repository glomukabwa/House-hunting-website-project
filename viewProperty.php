<?php
include 'config.php';

$houseId = isset($_GET['houseId']) ? (int)$_GET['houseId'] : 0;

if ($houseId <= 0) {
    echo "Invalid house ID.";
    exit;
}

// Get house info
$houseQuery = "
    SELECT h.*, c.caretakerName, c.caretakerPhoneNumber, c.caretakerEmail
    FROM House h
    LEFT JOIN Caretaker c ON h.caretakerId = c.caretakerId
    WHERE h.houseId = $houseId AND h.isApproved = 1
";
$houseResult = $conn->query($houseQuery);
$house = $houseResult->fetch_assoc();

if (!$house) {
    echo "House not found or not approved.";
    exit;
}

// Get house images
$imageQuery = "SELECT imageUrl FROM HouseImages WHERE houseId = $houseId";
$imageResult = $conn->query($imageQuery);
$images = $imageResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($house['houseTitle']); ?> - Details</title>
    <link rel="icon" type="icon" href="hhw-images/hhw-favicon.png">
    <link rel="stylesheet" href="stylesheet1.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/slanding.css">
</head>
<body class="studentSearchResults-body">

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
    <h2 class="ssrp-h2">HOUSE DETAILS</h2>

    <div class="row">
        <div class="column">
            <h3 style="color: rgb(111, 77, 56);">HOUSE ID: <?php echo $house['houseId']; ?></h3>

            <div class="img-column">
                <?php
                if (count($images) > 0) {
                    foreach ($images as $img) {
                        echo '<img class="myImg" src="' . htmlspecialchars($img['imageUrl']) . '" alt="House Image">';
                    }
                } else {
                    echo '<img class="myImg" src="images/default-placeholder.png">';
                }
                ?>
            </div>

            <div class="detailshouse">
                <p><strong>Title:</strong> <?php echo htmlspecialchars($house['houseTitle']); ?></p>
                <p><strong>Price:</strong> Ksh <?php echo number_format($house['housePrice'], 2); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($house['houseLocation']); ?></p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($house['houseDescription'])); ?></p>
                <p><strong>Caretaker:</strong> 
                    <?php echo htmlspecialchars($house['caretakerName']) . ' | ' .
                               htmlspecialchars($house['caretakerPhoneNumber']) . ' | ' .
                               htmlspecialchars($house['caretakerEmail']); ?>
                </p>
            </div>

            <input type="button" value="MAKE INQUIRY" onclick="window.location.href='StudentInquiry.php?houseId=<?php echo $houseId; ?>'" class="ssrp-button">
        </div>
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
            <p>Nairobi, Kenya</p>
        </div>
    </footer>
            </div>

<!-- Modal viewer -->
<div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div class="slide-number" id="slideNumber"></div> 
    <a class="prev">&#10094;</a>
    <a class="next">&#10095;</a>
</div>

<script src="script1.js"></script>

</body>
</html>
