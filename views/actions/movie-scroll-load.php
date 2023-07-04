<?php
//header('Content-Type: application/json');
$conn = dbConnect();
$tmdb = setupTMDB();

$query = "SELECT * FROM movies ORDER BY movie_title ASC";
$result = $conn->query($query);

$movieRow = [];

if ($result->num_rows > 0) {
    // Es gibt mindestens einen Film des Genres

    while ($movie = $result->fetch_assoc()) {
        $movieRow[] = movie_card($movie);  
    }
} else {
    $movieRow = '';
}

$jsonResponse = json_encode($movieRow); // Hier wird der JSON-Response generiert
$jsonResponse = str_replace(array("\r", "\n"), '', $jsonResponse);
echo $jsonResponse;