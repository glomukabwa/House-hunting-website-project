<?php
// This code is just for inserting a rejected house into the RejectedHouse table and deleting it from PendingHouse.
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $reason = $_POST['reason'] ?? 'No reason provided';

    $stmt = $conn->prepare("SELECT * FROM PendingHouse WHERE pendingHouseId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $house = $result->fetch_assoc();

    if ($house) {
        $stmtInsert = $conn->prepare("INSERT INTO RejectedHouse (pendingHouseId, houseTitle, houseLocation, housePrice, houseDescription, imageUrl, caretakerId, rejectionReason) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
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

        if (!$stmtInsert->execute()) {
            echo "Error inserting: " . $stmtInsert->error;
            exit;
        }

        $stmtDelete = $conn->prepare("DELETE FROM PendingHouse WHERE pendingHouseId = ?");
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();

        // Returning to admin page
        header("Location: admin.php?msg=house_rejected");
        exit;
    } else {
        echo "House not found.";
    }
}
?>
