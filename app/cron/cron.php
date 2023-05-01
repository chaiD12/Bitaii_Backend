<?php
// Connect to the database
$servername = "/opt/lampp/var/mysql/mysql.sock";
$username = "root";
$password = "";
$dbname = "dbdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the current date and time
$currentDate = date('Y-m-d H:i:s');

// Query to update the spin column
$sql = "UPDATE tb_user SET spin = 2 TIMESTAMPDIFF(MINUTE, last_spin_update, '$currentDate') >= 1";

// Execute the query
if (mysqli_query($conn, $sql)) {
    echo "Spin updated successfully";
} else {
    echo "Error updating spin: " . mysqli_error($conn);
}




// Close the database connection
mysqli_close($conn);
?>