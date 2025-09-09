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

    if ($result->num_rows > 0) {//Used to check the number of rows that have been returned by a query
        //Plz note that the above and if($result) are not the same
        //if($result) checks if the query has been executed successfully so even if the number of 
        //rows returned are 0, it indicates that it is succesful but num_rows checks the number of rows returned
        //In this case, we are doing that to check if the image is in PendingHouseImages
        //Before we do this: $row = $result->fetch_assoc() , the result is in forms of a table. 
        //The fetch_assoc() converts it to an associative array to make it easy to use
        while ($row = $result->fetch_assoc()) {
        //fetch_assoc fetches a row from the table in the result we have gotten above
        //while checks if the fetch_assoc() is  returning arrays. If it returns null which mean there is no more data in the table, the while becomes false so we stop
        //Why do we need to iterate? So imagine we have a table like:
        // 1 => image1.jpg
        // 1 => image2.jpg
        //fetch_assoc will fetch the first one then will give the image the src as indicated below so that the image is displayed
        //If we don't iterate, it will only fetch the first image and then stop and in out database, a house can have many images
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
