<?php
$conn = dbConnect();

$highlight_ID = intval($_POST['highlightID']);
$status = intval($_POST['status']);

$sql = "UPDATE highlights
SET highlight_status=$status
WHERE highlight_id=$highlight_ID";

$conn->query($sql);
$conn->close();
?>