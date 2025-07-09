<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {//Once the form is submitted, we check if the request method is POST.(Ensure it is the method in your form)
    //We check the server method to To prevent accidental execution .Without this check, the code could run every time the page is loaded, even without a form submission. That could cause unintended database updates or actions.
    //We also do it to To separate GET and POST logic:
    //GET is usually used to load pages or fetch data.
    //POST is used to submit data (e.g., forms).
    //You don’t want to run insert/update logic just by clicking a link or typing a URL.
    //Avoid errors or blank data. If you don’t check the method, and the user hasn't submitted anything, then $_POST['id'] and others might be undefined — leading to warnings or incorrect behavior.
    //Security. Limiting database changes (like verification, deletion, or password reset) to POST only helps reduce the risk of URL manipulation or accidental actions.

    $id = $_POST['id'];//We access the id like this. Rememeber, the name we've given the hidden input in the form is 'id'. So we assign it to id not caretakerId.

    // Set caretaker to verified
    $stmt = $conn->prepare("UPDATE caretaker SET isVerified = TRUE, verificationDate = NOW() WHERE caretakerId = ?");//? is a placeholder for the id value. 
    $stmt->bind_param("i", $id);//We bind the placeholder(?) to the actual value of the id. "i" indicates that the parameter is an integer. You use "s" for string, "d" for double, etc.

    //In php, the order is prepare, bind_param, then execute.
    if ($stmt->execute()) {//Here, you are telling the statement to execute and if it does, do this:
        echo "<script>alert('Caretaker verified successfully.'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Verification failed.'); window.location.href='admin.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
