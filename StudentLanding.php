<?php include 'config.php'; 

// Handle search input
$location = $_GET['location'] ?? '';
$maxPrice = $_GET['maxPrice'] ?? '';
$minPrice = $_GET['minPrice'] ?? '';

// Base query
$query = "
    SELECT h.houseId, h.houseTitle, h.houseLocation, h.housePrice, hi.imageUrl
    FROM House h
    LEFT JOIN (
        SELECT houseId, MIN(imageUrl) AS imageUrl
        FROM HouseImages
        GROUP BY houseId
    ) hi ON h.houseId = hi.houseId
    WHERE h.isApproved = 1
";

// Add filters only if a search was made
$hasSearch = !empty($location) || !empty($maxPrice) || !empty($minPrice);

if (!empty($location)) {
    $locationEscaped = $conn->real_escape_string($location);
    $query .= " AND h.houseLocation LIKE '%$locationEscaped%'";
}
if (!empty($maxPrice)) {
    $query .= " AND h.housePrice <= " . (float)$maxPrice;
}
if (!empty($minPrice)) {
    $query .= " AND h.housePrice >= " . (float)$minPrice;
}

$query .= " ORDER BY h.approvalDate DESC";

$result = $conn->query($query);
$houses = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Landing Page</title>
    <link rel="icon" type="icon" href="hhw-images/hhw-favicon.png">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/slanding.css">
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
        <section class="search">
            <form action="" method="GET" class="search">
                <p>SEARCH:</p>
                <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
                <input type="text" name="maxPrice" placeholder="Maximum Price" value="<?php echo htmlspecialchars($maxPrice); ?>">
                <input type="text" name="minPrice" placeholder="Minimum Price" value="<?php echo htmlspecialchars($minPrice); ?>">
                <button type="submit" class="searchbutton">ENTER</button>
            </form>
        </section>
        
        <p class="available">AVAILABLE HOUSES:</p><br>

        <section class="housepictures">
            <a href="#">
        <?php
        if (count($houses) > 0) {
            $count = 0;
            echo '<div class="house-row">';
            foreach ($houses as $row) {
                $image = !empty($row['imageUrl']) ? $row['imageUrl'] : 'images/default-placeholder.png';
                echo '<div class="house-card">
                        <img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($row['houseTitle']) . '">
                        <div class="house-details">
                            <p><strong>' . htmlspecialchars($row['houseTitle']) . '</strong></p>
                            <p>Location: ' . htmlspecialchars($row['houseLocation']) . '</p>
                            <p>Price: Ksh ' . number_format($row['housePrice'], 2) . '</p>
                        </div>
                      </div>';
                $count++;
                if ($count % 4 == 0 && $count < count($houses)) {
                    echo '</div><div class="house-row">';
                }
            }
            echo '</div>'; // close last row
        } else {
            echo "<p>No houses available.</p>";
        }
        ?>
            </a>
        </section>

        
        <a href="view_inquiries.php" class="viewinquiriesbtn">View Inquiries</a>


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
            <p>Nairobi, Kenya</p>
        </div>
    </footer>
</div>
</body>
</html>
