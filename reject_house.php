<?php
// This code deletes rejected houses from Pending houses and adds them to the RejectedHouse table.
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $reason = $_POST['reason'] ?? 'No reason provided';

    // Fetch the pending house data
    $stmt = $conn->prepare("SELECT * FROM PendingHouse WHERE pendingHouseId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $house = $result->fetch_assoc();
    $stmt->close();

    if ($house) {
        // Insert into RejectedHouse table
        $stmtInsert = $conn->prepare("INSERT INTO RejectedHouse (
            pendingHouseId, houseTitle, houseLocation, housePrice, 
            houseDescription, imageUrl, caretakerId, rejectionReason
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("issdssis",
            $house['pendingHouseId'],
            $house['houseTitle'],
            $house['houseLocation'],
            $house['housePrice'],
            $house['houseDescription'],
            $house['imageUrl'],
            $house['caretakerId'],
            $reason
        );
        $stmtInsert->execute();
        $stmtInsert->close();

        // Delete images from PendingHouseImages
        $delImgs = $conn->prepare("DELETE FROM PendingHouseImages WHERE houseId = ?");
        $delImgs->bind_param("i", $id);
        $delImgs->execute();
        $delImgs->close();

        // Delete house from PendingHouse
        $stmtDelete = $conn->prepare("DELETE FROM PendingHouse WHERE pendingHouseId = ?");
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();
        $stmtDelete->close();

        // Redirect to admin page with success message
        header("Location: admin.php?msg=house_rejected");
        exit;
    } else {
        echo "House not found.";
    }
}
?>
