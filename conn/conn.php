<?php
$conn = new Mysqli('Localhost', 'root', '', 'test123')

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>