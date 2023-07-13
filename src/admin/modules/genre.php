<?php
function initGenres() {
    $conn = dbConnect();
    $tmdb = setupTMDB();

    $genres = $tmdb->getGenres();

    $data = [];
    foreach ($genres as $genre) {
        $data[] = "(".$genre->getID().", '".$genre->getName()."')";
    }

    $dataSring = json_encode($data, JSON_UNESCAPED_UNICODE);
    $dataSring = str_replace(array('[', ']', "[", "]"), '', $dataSring);
    $dataSring = str_replace('"(', '(', $dataSring);
    $dataSring = str_replace(')"', ')', $dataSring);
    $dataSring = stripslashes($dataSring);
 
    $sql = "INSERT INTO genres (genre_id, genre_name) VALUES $dataSring";
    $result = $conn->query($sql);

    if (!$result) {
        set_callout('alert','genres_created_alert');
        page_redirect("/admin/genres");
    } else {
        set_callout('success','genres_created_success');
        page_redirect("/admin/genres");
    }
}

function genreCheck() {
    $conn = dbConnect();
    $sql = "SELECT id FROM genres";
    $results = $conn->query($sql);
    
    if ($results->num_rows > 0) {
        $conn->close();
        return true;
    } else {
        $conn->close();
        return false;
    }
}

function getDBGenreNameByID($id) {
    $conn = dbConnect();
    $sql = "SELECT genre_name FROM genres WHERE genre_id='$id'";
    $results = $conn->query($sql);
    if ($results->num_rows > 0) {
        while ($genre = $results->fetch_assoc()) {
            if ( isset($genre['genre_name']) ) {
                $conn->close();
                return $genre['genre_name'];
            }
        }
    }
}
?>