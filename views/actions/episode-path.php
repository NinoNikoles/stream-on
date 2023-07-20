<?php
$path = $_POST['path'];
$mediaID = intval($_POST['mediaID']);

$conn = dbConnect();

$sql = "UPDATE episodes SET file_path='$path' WHERE tmdbID=$mediaID;";

if (!($conn->query($sql) === TRUE)) {
    die('Error creating table: ' . $conn->error);
}
?>