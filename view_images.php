<?php
include 'config.php';

if (isset($_GET['houseId'])) {
    $houseId = intval($_GET['houseId']);//This works the same as (int)houseId however:
    //intval has an additional method which is base eg intval(number, 2) tells compiler to interpret number as a binary so:
    //intval("10" ,2) //10 is a binary
    //intval("77", 8) //77 is interpreted as octal
    //2 -> binary, 8 -> octal, 10 -> decimal, 16 -> hexadecimal

    // First check in PendingHouseImages
    $stmt = $conn->prepare("SELECT imageUrl FROM PendingHouseImages WHERE houseId = ?");
    $stmt->bind_param("i", $houseId);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Images for House ID: {$houseId}</h2>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<img src='{$row['imageUrl']}' alt='House Image' style='width:200px; margin:10px; border-radius:8px;'>";
        }
    } else {
        // If not found in pending, check in approved
        $stmt = $conn->prepare("SELECT imageUrl FROM HouseImages WHERE houseId = ?");
        $stmt->bind_param("i", $houseId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<img src='{$row['imageUrl']}' alt='House Image' style='width:200px; margin:10px; border-radius:8px;'>";
            }
        } else {
            echo "<p>No images found for this house.</p>";
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p>No house ID provided.</p>";
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
