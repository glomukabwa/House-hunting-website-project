<?php
session_start();
include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['hTitle'];
    $price = $_POST['hPrice'];
    $location = $_POST['hLocation'];
    $description = $_POST['hDescription']; 

    if (!isset($_SESSION['caretakerId'])) {
    echo "<script>alert('Not logged in.'); window.location.href='login.php';</script>";
    exit();
    }

    $caretakerId = $_SESSION['caretakerId'];

    // Verify caretaker is approved
    $check = $conn->prepare("SELECT isVerified FROM caretaker WHERE caretakerId = ?");
    $check->bind_param("i", $caretakerId);
    $check->execute();
    $result = $check->get_result();
    $caretaker = $result->fetch_assoc();

    if (!$caretaker || !$caretaker['isVerified']) {
        echo "<script>alert('Your account is not yet verified.'); window.location.href='CaretakerLandingPage.php';</script>";
    exit();
    }


    $caretakerId = $_SESSION['caretakerId'];

    // Insert house into PendingHouse first
    $stmt = $conn->prepare("INSERT INTO PendingHouse (houseTitle, houseLocation, housePrice, houseDescription, caretakerId) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $title, $location, $price, $description, $caretakerId);

    if ($stmt->execute()) {
        $houseId = $stmt->insert_id; // Get the ID of the inserted house

        $targetDir = "uploads/";
        $uploadOk = true;
        $imageCount = count($_FILES['hImgs']['name']);

        for ($i = 0; $i < $imageCount; $i++) {
            $imageName = basename($_FILES["hImgs"]["name"][$i]);
            $targetFile = $targetDir . $imageName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["hImgs"]["tmp_name"][$i]);
            if ($check === false || $_FILES["hImgs"]["size"][$i] > 2 * 1024 * 1024 || !in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<script>alert('One or more images are invalid.');</script>";
                $uploadOk = false;
                break;
            }

            if (move_uploaded_file($_FILES["hImgs"]["tmp_name"][$i], $targetFile)) {
                $stmtImg = $conn->prepare("INSERT INTO HouseImages (houseId, imageUrl) VALUES (?, ?)");
                $stmtImg->bind_param("is", $houseId, $targetFile);
                $stmtImg->execute();
            }
        }

        if ($uploadOk) {
            echo "<script>alert('House listing and images uploaded successfully.'); window.location.href='CaretakerLandingPage.php';</script>";
        } else {
            echo "<script>alert('Some images failed to upload.');</script>";
        }

    } else {
        echo "<script>alert('Error inserting house listing.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add House Listing</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/houselisting.css">
</head>
<body>
    <section class="response">
        <p class="title">ADD HOUSE LISTING</p>
     <form action="" method="post" enctype="multipart/form-data">
        <label for="hTitle">Enter house title:</label><br>
        <input type="text" id="hTitle" name="hTitle" required><br><br>

        <label for="hPrice">Enter house price:</label><br>
        <input type="number" step="0.01" id="hPrice" name="hPrice" required><br><br>

        <label for="hLocation">Enter house location:</label><br>
        <input type="text" id="hLocation" name="hLocation" required><br><br>

        <label for="hDescription">Enter house description:</label><br>
        <textarea id="hDescription" name="hDescription" required></textarea><br><br>

        <label for="hImgs">Attach house images:</label><br>
        <input type="file" id="hImgs" name="hImgs[]" accept="image/*" multiple required><br><br>

        <button type="submit">ENTER</button>
     </form>
    </section>
</body>
</html>
