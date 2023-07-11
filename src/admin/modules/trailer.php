<?php


function getTrailer($movieID, $extraClass="") {
    $conn = dbConnect();
    $sql = "SELECT movie_trailer FROM movies WHERE movie_tmdbID=$movieID";
    $result = $conn->query($sql)->fetch_assoc();
    
    if ( isset($result['movie_trailer']) ) {
        $trailerID = $result['movie_trailer'];
        $iframe = '<figure class="widescreen '.$extraClass.'"><iframe id="ytplayer-'.$movieID.'" type="text/html" src="http://www.youtube.com/embed/'.$trailerID.'?enablejsapi=1" frameborder="0"></iframe></figure>';

        $conn->close();
        return $iframe;
    }
}

function getHighlight() {
    $conn = dbConnect();
    $sql = "SELECT movie_tmdbID, movie_poster, movie_thumbnail FROM movies INNER JOIN highlights ON movies.movie_tmdbID=highlights.movie_id WHERE highlights.highlight_status=1";
    if ( $conn->query($sql)->num_rows > 0) {

        $movieID = $conn->query($sql)->fetch_assoc()['movie_tmdbID'];
        $poster = $conn->query($sql)->fetch_assoc()['movie_poster'];
        $backdrop = $conn->query($sql)->fetch_assoc()['movie_thumbnail'];
    
        $hightlight = "
        <figure class='widescreen'>
            <img src=".loadImg('original', $backdrop)." loading='lazy'>
        </figure>
        <figure class='poster'>
            <img src=".loadImg('original', $poster)." loading='lazy'>
        </figure>";
    
        $conn->close();
        echo '<div class="highlight-wrapper">'.$hightlight.'</div>';
    }  
}