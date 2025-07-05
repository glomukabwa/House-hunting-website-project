<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $reason = $_POST['reason'] ?? 'No reason provided';

    $stmt = $conn->prepare("SELECT * FROM caretaker WHERE caretakerId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $caretaker = $result->fetch_assoc();

    if ($caretaker) {
        $stmtInsert = $conn->prepare("INSERT INTO RejectedCaretaker (caretakerId, caretakerName, caretakerEmail, caretakerPhoneNumber, rejectionReason) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("issss",
            $caretaker['caretakerId'],
            $caretaker['caretakerName'],
            $caretaker['caretakerEmail'],
            $caretaker['caretakerPhoneNumber'],
            $reason
        );

        if (!$stmtInsert->execute()) {
            echo "Error inserting: " . $stmtInsert->error;
            exit;
        }

        $stmtDelete = $conn->prepare("DELETE FROM caretaker WHERE caretakerId = ?");
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();

        // Return to admin page
        header("Location: admin.php?msg=caretaker_rejected");
        exit;
    } else {
        echo "Caretaker not found.";
    }
}
?>
