<?php
$conn = dbConnect();

$genreID = $_POST['genreID'];
$order = $_POST['order'];
$type = $_POST['type'];
$mediaCard = "";

if ( $genreID === 'all' ) {
    $sql = "SELECT * FROM media 
    WHERE mediaType = '$type' ORDER BY title $order;";
} else {
    $genreID = intval($genreID);

    $sql = "SELECT m.* FROM media AS m 
    JOIN media_genre AS mg ON m.tmdbID = mg.media_id
    WHERE mg.genre_id = $genreID AND m.mediaType = '$type' ORDER BY m.title $order;";
}

$result = $conn->query($sql);
if ( $result->num_rows > 0 ) {
    while ( $media = $result->fetch_assoc() ) {
        $mediaCard .= media_card($media, 'col-6 col-4-xsmall col-3-medium grid-padding');
    }

    echo $mediaCard;
} else {
    echo '<div class="col12 grid-padding"><p>'.lang_snippet('no_content_found').'</p></div>';
}