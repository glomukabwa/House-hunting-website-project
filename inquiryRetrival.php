<?php
session_start(); 

$servername = "localhost";
$username = "root";
$password = ""; 
$database = "househuntingwebsitedb"; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['caretakerId'])) {
    die("Caretaker not logged in.");
}

$caretakerId = $_SESSION['caretakerId'];

$sql = "SELECT inquiry.inquiryId, inquiry.studentId, inquiry.houseId, 
               inquiry.inquiryDate, inquiry.inquiryStatus
        FROM inquiry
        JOIN house ON inquiry.houseId = house.houseId
        WHERE house.caretakerId = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $caretakerId);
$stmt->execute();

$result = $stmt->get_result();

?>
