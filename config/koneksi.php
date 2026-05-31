<?php

$conn = new mysqli('localhost', 'root', '','pbw');
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
?>