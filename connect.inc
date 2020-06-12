<?php
$con = mysqli_connect("localhost", "b1accdeb138018", "b3c02009", "heroku_402f23a0009884e");
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

mysqli_set_charset($con, "utf8");
?>