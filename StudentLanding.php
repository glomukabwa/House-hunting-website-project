<?php include 'config.php'; 

// Handle search input
$location = $_GET['location'] ?? '';
$maxPrice = $_GET['maxPrice'] ?? '';
$minPrice = $_GET['minPrice'] ?? '';

$query = "SELECT * FROM House WHERE isApproved = 1";

if (!empty($location)) {
    $query .= " AND houseLocation LIKE '%" . $conn->real_escape_string($location) . "%'";
}
if (!empty($maxPrice)) {
    $query .= " AND housePrice <= " . (float)$maxPrice;
}
if (!empty($minPrice)) {
    $query .= " AND housePrice >= " . (float)$minPrice;
}

$query .= " ORDER BY houseId DESC LIMIT 4";

$result = $conn->query($query);
$houses = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Landing Page</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
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
                <input type="text" name="location" placeholder="Location">
                <input type="text" name="maxPrice" placeholder="Maximum Price">
                <input type="text" name="minPrice" placeholder="Minimum Price">
                <button type="submit" class="searchbutton">ENTER</button>
            </form>
        </section>
        
        <p class="available">AVAILABLE HOUSES:</p>
        
        <section class="housepictures">
        <?php
        $sql = "
            SELECT h.houseId, h.houseTitle, hi.imageUrl
            FROM House h
            LEFT JOIN (
                SELECT houseId, MIN(imageUrl) AS imageUrl
                FROM HouseImages
                GROUP BY houseId
            ) hi ON h.houseId = hi.houseId
            WHERE h.isApproved = 1
            ORDER BY h.approvalDate DESC
            LIMIT 4
       ";

       $result = $conn->query($sql);

       if ($result && $result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
                $image = $row['imageUrl'] ?? 'images/default-placeholder.png'; // We use ?? 'default-placeholder.png' to avoid errors if a house somehow has no images (it is an optional safe option)
                echo '<a href="#"><img src="' . $image . '" alt="House"></a>';
            }
        } else {
            echo "<p>No houses available.</p>";
        }

        ?>
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