<?php 
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];

    // Step 1: Get data from PendingHouse
    $stmt = $conn->prepare("SELECT * FROM PendingHouse WHERE pendingHouseId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $house = $result->fetch_assoc();
    $stmt->close();

    if ($house) {
        // Step 2: Insert into House
        $insert = $conn->prepare("
            INSERT INTO House (houseTitle, houseLocation, housePrice, houseDescription, isApproved, approvalDate, caretakerId)
            VALUES (?, ?, ?, ?, 1, CURDATE(), ?)
        ");
        $insert->bind_param(
            "ssdsi", 
            $house['houseTitle'], 
            $house['houseLocation'], 
            $house['housePrice'], 
            $house['houseDescription'], 
            $house['caretakerId']
        );
        $insert->execute();

        $newHouseId = $insert->insert_id;
        $insert->close();

        // Step 3: Copy images from PendingHouseImages to HouseImages
        $imgQuery = $conn->prepare("SELECT imageUrl FROM PendingHouseImages WHERE houseId = ?");
        $imgQuery->bind_param("i", $id);
        $imgQuery->execute();
        $imgResult = $imgQuery->get_result();

        while ($img = $imgResult->fetch_assoc()) {
            $copy = $conn->prepare("INSERT INTO HouseImages (houseId, imageUrl) VALUES (?, ?)");
            $copy->bind_param("is", $newHouseId, $img['imageUrl']);
            $copy->execute();
            $copy->close();
        }

        $imgQuery->close();

        // Step 4: Delete from PendingHouseImages
        $delImg = $conn->prepare("DELETE FROM PendingHouseImages WHERE houseId = ?");
        $delImg->bind_param("i", $id);
        $delImg->execute();
        $delImg->close();

        // Step 5: Delete from PendingHouse
        $delete = $conn->prepare("DELETE FROM PendingHouse WHERE pendingHouseId = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
        $delete->close();

        echo "<script>alert('House approved and moved to listings.'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('House not found.'); window.location.href = 'admin.php';</script>";
    }

    $conn->close();
}
?>
