<?php
include 'config.php';

if (isset($_GET['houseId'])) {
    $houseId = intval($_GET['houseId']);

    $stmt = $conn->prepare("SELECT imageUrl FROM HouseImages WHERE houseId = ?");
    $stmt->bind_param("i", $houseId);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Images for House ID: $houseId</h2>";

    if ($result->num_rows > 0) {
        echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";
        while ($row = $result->fetch_assoc()) {
            echo "<img src='{$row['imageUrl']}' width='300' style='border: 1px solid #ccc; padding: 10px;'>";
        }
        echo "</div>";
    } else {
        echo "<p>No images found for this house.</p>";
    }

    echo "<br><a href='admin.php'>Back to Admin Page</a>";

    $stmt->close();
} else {
    echo "Invalid house ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Images Page</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/view_images.css">
</head>
<body></body>
