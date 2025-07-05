<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Set caretaker to verified
    $stmt = $conn->prepare("UPDATE caretaker SET isVerified = TRUE, verificationDate = NOW() WHERE caretakerId = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Caretaker verified successfully.'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Verification failed.'); window.location.href='admin.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
