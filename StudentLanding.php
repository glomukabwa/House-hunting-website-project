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
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/slanding.css">
</head>
<body>
    <div class="wrapper">
        <header>
        <div class="logo">
            <p>LOGO</p>
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
        $sql = "SELECT * FROM House WHERE isApproved = 1 ORDER BY approvalDate DESC LIMIT 4";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo '<a href="#"><img src="' . $row["image_url"] . '" alt="House"></a>';
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