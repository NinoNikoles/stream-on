<?php
function scrollLoader($media, $count) {
    $conn = dbConnect();

    $count = intval($count);

    $sql = "SELECT * FROM $media ORDER BY movie_title ASC LIMIT 20 OFFSET $count";
    $results = $conn->query($sql);

    $data = [];
    $i = 0;

    if ($results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {

            $data[$i]['movie_tmdbID'] = $row['movie_tmdbID'];
            $data[$i]['movie_title'] = $row['movie_title'];            
            $data[$i]['movie_tagline'] = $row['movie_tagline'];
            $data[$i]['movie_overview'] = $row['movie_overview'];
            $data[$i]['movie_poster'] = $row['movie_poster'];
            $data[$i]['movie_thumbnail'] = $row['movie_thumbnail'];
            $data[$i]['movie_rating'] = $row['movie_rating'];
            $data[$i]['movie_release'] = $row['movie_release'];
            $data[$i]['movie_runtime'] = $row['movie_runtime'];
            $data[$i]['movie_collection'] = $row['movie_collection'];
            $data[$i]['movie_genres'] = $row['movie_genres'];

            $genres = json_decode($data[$i]['movie_genres']);

            $dataGenres = [];
            foreach ( $genres as $genre ) {
                $dataGenres[] = array(
                    'id' => $genre,
                    'name' => getDBGenreNameByID($genre)
                );
            }

            $data[$i]['genres'] = $dataGenres;

            $i++;
        }
    }

    return $data;
}
?>