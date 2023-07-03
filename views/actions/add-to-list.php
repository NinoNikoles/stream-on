<?php
$conn = dbConnect();

$movieID = intval($_POST['movieID']);
$userID = intval($_SESSION['userID']);
$type = $_POST['type'];

if ( $type === 'add' ) {
    $sql = "INSERT INTO watchlist(user_id, movie_id) VALUES
    ($userID, $movieID)
    ON DUPLICATE KEY UPDATE movie_id = VALUES(movie_id)";
    $conn->query($sql);
} else if ( $type === 'remove' ) {
    $sql = "DELETE FROM watchlist WHERE user_id='$userID' and movie_id='$movieID'";
    $conn->query($sql);
}
