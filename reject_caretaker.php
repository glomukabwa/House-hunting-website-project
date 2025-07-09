<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);//Converts the input which is a string to an integer.
    $reason = $_POST['reason'] ?? 'No reason provided';

    $stmt = $conn->prepare("SELECT * FROM caretaker WHERE caretakerId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();//This is important because if you don't put it here, it will try to get the results in the next line and there will be no results to get, leading to an error.
    //Rememeber, the order is prepare, bind_param, then execute.
    $result = $stmt->get_result();
    $caretaker = $result->fetch_assoc();

    if ($caretaker) {
        $stmtInsert = $conn->prepare("INSERT INTO RejectedCaretaker (caretakerId, caretakerName, caretakerEmail, caretakerPhoneNumber, rejectionReason) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("issss",//i means integer and s means string for the remaining parameters.
            $caretaker['caretakerId'],
            $caretaker['caretakerName'],
            $caretaker['caretakerEmail'],
            $caretaker['caretakerPhoneNumber'],
            $reason
        );

        if (!$stmtInsert->execute()) {//Here, you are saying that if the insert fails, do this meaning u're indirectly telling it to execute the insert statement and check if it was successful or not.
            echo "Error inserting: " . $stmtInsert->error;
            //$stmtInsert->error; gives you an error message explaining an issue with the latest prepared statement execution. In this case, it is a prepared statement for insertion so it  will tell you what went wrong with your insertion.
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
