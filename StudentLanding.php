<?php include 'config.php'; 

// Handle search input
$location = $_GET['location'] ?? '';
//GET method is used for retrieving data sent using the URL query string
//In the form, I've used the GET method to send the search parameters so this allows me to use the $_GET method
// The question marks (??) are a short form of an if else statement. In this context they mean if the location is provided in the URL, store it in $location, else set $location to be an empty string.
$maxPrice = $_GET['maxPrice'] ?? '';
$minPrice = $_GET['minPrice'] ?? '';

// Base query
$query = "
    SELECT h.houseId, h.houseTitle, h.houseLocation, h.housePrice, hi.imageUrl
    FROM House h
    LEFT JOIN (
        SELECT houseId, MIN(imageUrl) AS imageUrl
        FROM HouseImages
        GROUP BY houseId
    ) hi ON h.houseId = hi.houseId
    WHERE h.isApproved = 1
";
//In the above query, h represents the House table while hi represents the HouseImages table.

//About JOINs:
//The LEFT JOIN is used to combine rows from both tables based on the houseId.
//The LEFT JOIN usually joins the left table(House in this case) with the right table(HouseImages in this case) and returns all records from the left table and the matched records from the right table.
//We don't use eg INNER JOIN because the principle of INNER JOIN is that it returns only matching rows from both tables
//Since both tables have IDs and that's what we use to JOIN them, if a house didn't have images so would not be displayed in House images, it wouldn't be shown. It would only return houses that have images, excluding those without images.
//RIGHT JOIN could still be used but in a reversed manner, where the right table would be the HouseImages and the left table would be the House.
//FULL OUTER JOIN would return all records from both tables, but in this case, we only want houses that are approved and their images if they exist so this would not give us what we want.

//MIN() is used to get the smallest,first or lowest value in a column.
//In this case, we want the first image of each house so we use it.


// Add filters only if a search was made
$hasSearch = !empty($location) || !empty($maxPrice) || !empty($minPrice);

if (!empty($location)) {
    $locationEscaped = $conn->real_escape_string($location);
    //real_escape_string is used to escape special characters in a string for use in an SQL statement, preventing SQL injection attacks.
    //For example, if a user inputs a location with special characters like quotes or semicolons, this function will escape them to ensure the query runs correctly.
    //Eg I enter "Nairobi" as the location, it will be escaped to 'Nairobi' in the query.
    //If a hacker enters Nairobi' OR 1=1 --, the final query would look like this: SELECT * FROM House WHERE houseLocation = 'Nairobi' OR 1=1 --'
    //The first ' closes the original string ('Nairobi'), the OR 1=1 is always true(bcz 1 is always equal to 1) → this makes the condition match every row in the table, -- is a comment in SQL → everything after it is ignored.
    //So instead of filtering by location, your app just returns every house in the database.
    //If a hacker enters a malicious input like "Nairobi'; DROP TABLE House; --", the function will escape it to prevent the query from executing harmful commands.
    //The location will be escaped to 'Nairobi\'; DROP TABLE House; --', No house has this location so they will not be able to delete the House table.
    //So this function is very important for security.

    $query .= " AND h.houseLocation LIKE '%$locationEscaped%'";
    //This is where the filtering occurs.
    //The LIKE operator is used to search for a specified pattern in a column.
    //The % wildcard is used to match any sequence of characters, 
    // So LIKE '%$locationEscaped%' means search for any location that contains the entered text anywhere in the string.
    //.= is used to add to the existing query string.
    //AND is used to combine this condition with the previous one, which checks if the house is approved.(h.isApproved = 1)
    //So now the query looks like this:... WHERE h.isApproved = 1 AND h.housePrice >= 10000
    //AND adds on to the WHERE clause
}
if (!empty($maxPrice)) {
    $query .= " AND h.housePrice <= " . (float)$maxPrice;
    //(float)$maxPrice converts the maxPrice to ensure that you are comparing numbers, not strings.
    //This is important because if maxPrice is a string, it could lead to incorrect comparisons.
    //<= means less than or equal to
}
if (!empty($minPrice)) {
    $query .= " AND h.housePrice >= " . (float)$minPrice;
    //>= means greater than or equal to
}

$query .= " ORDER BY h.approvalDate DESC";
//.= is used to add to the existing query string.
//The results what we get from the above queries will be ordered by the approval date of the houses in descending order.
//DESC means descending order, so the most recently approved houses will appear first.
//We don't use AND here because we are not adding any more conditions, we are just ordering the results.
//.= doesn’t care what kind of SQL clause you're appending(adding) (whether it's AND, ORDER BY, etc.).
//We used AND in the ones above because we were adding conditions to the WHERE clause, but here we are just adding an ORDER BY clause to the end of the query.
//So it looks like this: ... WHERE h.isApproved = 1 AND h.housePrice >= 10000 ORDER BY h.approvalDate DESC

$result = $conn->query($query);
$houses = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
// ? is the Ternary Operator in PHP. See explanation after this set of comments.
// This query is saying if $result is truthy (i.e., not false, meaning the query was successful), then:
//Fetch all rows from the result as an associative array (with column names as keys) : (fetch_all(MYSQLI_ASSOC))
//Else:Assign an empty array [] to $houses :([])
//If $result is false (i.e., query failed), this avoids errors by safely setting $houses to an empty array.


// ? is also a shortform of an if else statement.
//The difference between this and ??(Null Coalescing Operator (Introduced in PHP 7) is that ?? checks if the variable is set and not null while ? checks if the result of the query is truthy (not false or null).
//For example,$name = $_GET['name'] ?? 'Guest'; Will check, if the GET method set, if it is not set or is null, it will assign 'Guest' to $name.
//To do the same thing with ? , you'd have to write it like this: $name = isset($_GET['name']) ? $_GET['name'] : 'Guest';
//With ? , you'd have to check if the GET method is set first(using isset()) and then assign the value or a default value.
//So it's almost similar to ??, but longer and more flexible since you can add more conditions if needed.
//With ?? , you can chain multiple values eg $_POST['x'] ?? $_GET['x'] ?? 'N/A'
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Landing Page</title>
    <link rel="icon" type="icon" href="hhw-images/hhw-favicon.png">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/slanding.css">
</head>
<body>
<div class="wrapper">
    <header>
        <div class="logo">
            <img src="hhw-images/hhw-logo.png" alt="Logo" class="logo-img">
        </div>
        <nav>
            <a href="#">HOME</a>
            <a href="#">ABOUT US</a>
            <a href="#">SIGN UP</a>
            <a href="#">LOG IN</a>
        </nav>
        <div class="profile">
            <img src="images/black.jpeg" alt="black">
        </div>
    </header>

    <main>
        <section class="search">
            <form action="" method="GET" class="search">
                <p>SEARCH:</p>
                <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
                <!-- The reason I've echoed the $location is so that if a user searches for a location, it will remain in the search box even after the page reloads. -->
                <!-- This makes the page user friendly because a user can see what they searched for and modify it if needed. -->
                <!-- htmlspecialchars() is used to convert special characters to HTML entities, preventing XSS attacks. -->
                <!-- For example, <script>alert('Hacked');</script> is a Javascript that if run, will create a pop-up alert saying 'Hacked'. This will scare a user -->
                <!-- However, if you use htmlspecialchars() on this string, it will convert the < and > characters to &lt; and &gt; respectively. -->
                <!-- So the above will be converted to &lt;script&gt;alert('Hacked');&lt;/script&gt; -->
                <!-- Other examples (" becomes &quot;) and (> becomes &gt;)-->
                <!--Using htmlspecialchars() ensures the above would show as plain text rather than executing a script.-->
                <input type="text" name="maxPrice" placeholder="Maximum Price" value="<?php echo htmlspecialchars($maxPrice); ?>">
                <input type="text" name="minPrice" placeholder="Minimum Price" value="<?php echo htmlspecialchars($minPrice); ?>">
                <button type="submit" class="searchbutton">ENTER</button>
            </form>
        </section>
        
        <p class="available">AVAILABLE HOUSES:</p><br>

        <section class="housepictures">
        <?php
        if (count($houses) > 0) {//Creates a loop for only if there are results meaning if there are houses available
            $count = 0;//Counts the number of houses displayed
            echo '<div class="house-row">';//Starts a new row for displaying houses
            foreach ($houses as $row) {//foreach is used for looping through arrays.
                // It works like this: foreach ($array as $value) { // code to execute }
                //So here, $houses is the array and $row is each individual house in that array. I should have given $row a better name like $house but don't let it confuse u.
                //So for each house, we will get its image, id:
                $image = !empty($row['imageUrl']) ? $row['imageUrl'] : 'images/default-placeholder.png';//If an image URL is empty, use a default placeholder image.
                $houseId = (int)$row['houseId'];

                //And then, display the house details in a card format.
                //(Card is a UI design pattern that displays information in a visually appealing way, like a card in a deck of cards.)
                //You have to style this card format and I've done it in css.
                //So if the above has been done, diplay the card in a href that leads to viewProperty.php with the houseId as a query parameter.
                //The href checks the ID before taking you to the respective viewProperty.php page.
                echo '<a href="viewProperty.php?houseId=' . $houseId . '" class="house-card">
                <img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($row['houseTitle']) . '">
                <div class="house-details">
                <p><strong>' . htmlspecialchars($row['houseTitle']) . '</strong></p>
                <p>Location: ' . htmlspecialchars($row['houseLocation']) . '</p>
                <p>Price: Ksh ' . number_format($row['housePrice'], 2) . '</p>
                </div>
                </a>';//The </div> closes the house-details div, and </a> closes the anchor tag.

                $count++;
                if ($count % 4 == 0 && $count < count($houses)) {//Once we've reached 4 houses, we close the current row and start a new one.
                    echo '</div><div class="house-row">';//This closes the previous row and starts a new one.
                }
            }
            echo '</div>'; // close last row. We put (<div class="house-row">) in the previous line to open a new row and now we are closing it.
        } else {
            echo "<p>No houses available.</p>"; //If the count = 0, meaning no houses were found, display this message.
        }
        ?>
            
        </section>

        
        <a href="view_inquiries.php" class="viewinquiriesbtn">View Inquiries</a>


    </main>

    <footer>
        <div class="sitemap">
            <a href="#">HOME</a>
            <a href="#">ABOUT US</a>
            <a href="#">SIGN UP</a>
            <a href="#">LOG IN</a>
        </div>
        <div class="contacts">
            <p>GET IN TOUCH WITH US</p>
            <p>webhunt@gmail.com</p>
            <p>+254178653987</p>
            <p>Nairobi, Kenya</p>
        </div>
    </footer>
</div>
</body>
</html>