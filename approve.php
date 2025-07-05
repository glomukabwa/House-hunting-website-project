<?php
// This file moves a record from PendingHouse to House.
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];

    // Get data from pending table
    $stmt = $conn->prepare("SELECT * FROM PendingHouse WHERE pendingHouseId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $house = $result->fetch_assoc();
    $stmt->close();

    if ($house) {
        // Insert into approved table
        $insert = $conn->prepare("INSERT INTO House (houseTitle, houseLocation, housePrice, houseDescription, imageUrl, isApproved, approvalDate, caretakerId) VALUES (?, ?, ?, ?, ?, 1, CURDATE(), ?)");
        $insert->bind_param("ssdssi", $house['houseTitle'], $house['houseLocation'], $house['housePrice'], $house['houseDescription'], $house['imageUrl'], $house['caretakerId']);
        $insert->execute();
        $insert->close();

        // Delete from pending
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
