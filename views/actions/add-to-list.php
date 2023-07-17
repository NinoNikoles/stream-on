<?php
$conn = dbConnect();

$mediaID = intval($_POST['mediaID']);
$userID = intval($_SESSION['userID']);
$type = $_POST['type'];

if ( $type === 'add' ) {
    $sql = "INSERT INTO watchlist(user_id, media_id) VALUES
    ($userID, $mediaID)
    ON DUPLICATE KEY UPDATE media_id = VALUES(media_id)";
    $conn->query($sql);
} else if ( $type === 'remove' ) {
    $sql = "DELETE FROM watchlist WHERE user_id='$userID' and media_id='$mediaID'";
    $conn->query($sql);
}
