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
        // Since I gave the input field name as "hImgs[]" it will be an array of images
        //$_FILES is a superglobal array in PHP that holds info about uploaded files.
        //$_FILES['hImgs']['name'] is an array of uploaded file names. Other examples:
        // $_FILES['hImgs']['tmp_name'] is an array of temporary file names.
        // $_FILES['hImgs']['size'] is an array of file sizes. etc
        //So we're using it to access the uploded images using their names then we're counting these images using count.

        for ($i = 0; $i < $imageCount; $i++) {
            //Step 1:Giving the image a name and a target directory
            $imageName = basename($_FILES["hImgs"]["name"][$i]);
            //basename() removes the directory path and returns only the file name. eg. house1.jpg
            $targetFile = $targetDir . $imageName; 
            //This tells the PHP to save the uploaded file in the "uploads" folder with the file name.
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            //PATHINFO_EXTENSION returns the file extension of the file name eg jpg, png, etc.
            //strtolower() converts the file extension to lowercase.


            //Step 2:Validity of the image(checking the size and type)
            $check = getimagesize($_FILES["hImgs"]["tmp_name"][$i]);
            //When a file is uploaded, it is stored in a temporary location on the server.
            //The temporary file name looks something like this: C:\xampp\tmp\php1234.tmp
            //The function getimagesize() follows this path and opens the file to check the width, height, and type of the image it is and it does this to confirm that the uploaded file is an image.

            if ($check === false || $_FILES["hImgs"]["size"][$i] > 2 * 1024 * 1024 || !in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                //The reason we still check the size again is to ensure it doesn't exceed a certain size eg here, we don't want it to surpass 2MBs.
                //The in_array() function checks if the file extension is one of the allowed types.

                echo "<script>alert('One or more images are invalid.');</script>";
                $uploadOk = false;
                break;
            }

            
            //Step 3:Actually uploading the image
            if (move_uploaded_file($_FILES["hImgs"]["tmp_name"][$i], $targetFile)) {
                //We're now moving the image from the temporary location to the target directory.
                //The $targetFile contains the $tagetDir and the $imageName. This is where step one will be used.
                $stmtImg = $conn->prepare("INSERT INTO PendingHouseImages (houseId, imageUrl) VALUES (?, ?)");
                $stmtImg->bind_param("is", $houseId, $targetFile);
                $stmtImg->execute();
            }
        }

        if ($uploadOk) {
            echo "<script>alert('House listing and images uploaded successfully.Await verification from administration'); window.location.href='AllInquiries.php';</script>";
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
