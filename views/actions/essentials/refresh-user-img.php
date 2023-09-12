<?php
$conn = dbConnect();

$userID = $_POST['userID'];
echo userProfileImg($userID);
?>