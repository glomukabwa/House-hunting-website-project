<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Rejected Caretakers</title>
    <link rel="icon" type="icon" href="hhw-images\hhw-favicon.png">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <h1>Rejected Caretakers</h1>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Reason</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM RejectedCaretaker");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['caretakerId']}</td>
                    <td>{$row['caretakerName']}</td>
                    <td>{$row['caretakerEmail']}</td>
                    <td>{$row['caretakerPhoneNumber']}</td>
                    <td>{$row['rejectionReason']}</td>
                </tr>";
        }
        ?>
    </table>
</body>
</html>
