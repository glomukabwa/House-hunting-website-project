<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Rejected Houses</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <h1>Rejected House Listings</h1>
    <table>
        <tr>
            <th>ID</th><th>Title</th><th>Location</th><th>Price</th><th>Description</th><th>Reason</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM RejectedHouse");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['pendingHouseId']}</td>
                    <td>{$row['houseTitle']}</td>
                    <td>{$row['houseLocation']}</td>
                    <td>{$row['housePrice']}</td>
                    <td>{$row['houseDescription']}</td>
                    <td>{$row['rejectionReason']}</td>
                </tr>";
        }
        ?>
    </table>
</body>
</html>
