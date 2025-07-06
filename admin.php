<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator's Page</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/admin.css">
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
        <h1>CARETAKER REQUESTS</h1>
     <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone No</th>
            <th>Status</th>
        </tr>

    <?php
    $caretakers = $conn->query("SELECT * FROM caretaker WHERE isVerified = FALSE");

    while ($row = $caretakers->fetch_assoc()) {
       echo "<tr>
            <td>{$row['caretakerId']}</td>
            <td>{$row['caretakerName']}</td>
            <td>{$row['caretakerEmail']}</td>
            <td>{$row['caretakerPhoneNumber']}</td>
            <td>
                <form method='POST' action='verify_caretaker.php' style='display:inline;'>
                    <input type='hidden' name='id' value='{$row['caretakerId']}'>
                    <input class='verify' type='submit' value='Verify'>
                </form>
                <br>
                <form method='POST' action='reject_caretaker.php' style='display:inline;'>
                    <input type='hidden' name='id' value='{$row['caretakerId']}'>
                    <input class='reject' type='submit' value='Reject'><br>
                    <input type='text' name='reason' placeholder='Reason for rejection' required>
                </form>
            </td>
          </tr>";
    }
    ?>
     </table>
     <br><br><br><br><br>

     <h1>HOUSE LISTING REQUESTS</h1>
     <table>
     <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Price</th>
        <th>Location</th>
        <th>CaretakerID</th>
        <th>Description</th>
        <th>Image</th>
        <th>Action</th>
     </tr>

    <?php
    $sql = "SELECT * FROM PendingHouse";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['pendingHouseId']}</td>
                <td>{$row['houseTitle']}</td>
                <td>{$row['housePrice']}</td>
                <td>{$row['houseLocation']}</td>
                <td>{$row['caretakerId']}</td>
                <td>{$row['houseDescription']}</td>
                <td>";

        $imgStmt = $conn->prepare("SELECT imageUrl FROM PendingHouseImages WHERE houseId = ? LIMIT 1");
        $imgStmt->bind_param("i", $row['pendingHouseId']);
        $imgStmt->execute();
        $imgResult = $imgStmt->get_result();
        $img = $imgResult->fetch_assoc();
        $imgStmt->close();

        $imgUrl = $img ? $img['imageUrl'] : 'images/default-placeholder.png';
        echo "<img src='{$imgUrl}' alt='Preview' style='width:100px; height:auto; border-radius:5px;'>
            <form method='GET' action='view_images.php'>
                <input type='hidden' name='houseId' value='{$row['pendingHouseId']}'>
                <input class='viewimages' type='submit' value='View All'>
            </form>
            </td>";

        echo "<td>
                    <form method='POST' action='approve.php' style='display:inline;'>
                        <input type='hidden' name='id' value='{$row['pendingHouseId']}'>
                        <input class='approve' type='submit' value='Approve'>
                    </form>
                    <br>
                    <form method='POST' action='reject_house.php' style='display:inline;'>
                        <input type='hidden' name='id' value='{$row['pendingHouseId']}'>
                        <input class='reject' type='submit' value='Reject'><br>
                        <input type='text' name='reason' placeholder='Reason for rejection' required>
                    </form>
                </td>
              </tr>";
        }
     ?>
     </table>

     <div style="text-align: center; margin: 20px;">
        <a href="RejectedHouses.php" class="button-link">View Rejected Houses</a> |
        <a href="RejectedCaretakers.php" class="button-link">View Rejected Caretakers</a>
     </div>
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
