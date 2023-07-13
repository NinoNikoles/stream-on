<?php
$conn = dbConnect();

$movieID = intval($_POST['movieID']);
$status = intval($_POST['status']);

$sql = "UPDATE highlights
SET highlight_status=$status
WHERE movie_id=$movieID";

$conn->query($sql);
$conn->close();
?>