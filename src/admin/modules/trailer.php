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
    $hightlightSelect = "SELECT highlights.highlight_id, media.type FROM highlights INNER JOIN media ON highlights.highlight_id = media.tmdbID WHERE highlights.highlight_status = 1 AND media.tmdbID IN (SELECT movies.movie_tmdbID FROM movies UNION SELECT shows.show_tmdbID FROM shows) ORDER BY RAND() LIMIT 1";
    $hightlightResult = $conn->query($hightlightSelect);

    if ( $hightlightResult->num_rows > 0) {
        while ( $highlight = $hightlightResult->fetch_assoc() ) {
            
            if ( $highlight['type'] === 'movie' ) {
                $movieID = $highlight['highlight_id'];
                $getMovie = "SELECT movie_tmdbID, movie_title, movie_overview, movie_poster, movie_thumbnail FROM movies WHERE movie_tmdbID=$movieID";
                $movieResults = $conn->query($getMovie);

                while ( $movie = $movieResults->fetch_assoc() ) {
                    $mediaID = $movie['movie_tmdbID'];
                    $title = $movie['movie_title'];
                    $description = $movie['movie_overview'];
                    $poster = $movie['movie_poster'];
                    $backdrop = $movie['movie_thumbnail'];
                }
            } else {
                $showID = $highlight['highlight_id'];
                $getShow = "SELECT show_tmdbID, show_title, show_overview, show_poster, show_thumbnail FROM shows WHERE show_tmdbID=$showID";
                $showResults = $conn->query($getShow);

                while ( $show = $showResults->fetch_assoc() ) {
                    $mediaID = $show['show_tmdbID'];
                    $title = $show['show_title'];
                    $description = $show['show_overview'];
                    $poster = $show['show_poster'];
                    $backdrop = $show['show_thumbnail'];
                }
            }
        }

        $hightlight = "
        <figure class='poster'>
            <img src=".loadImg('original', $poster)." loading='lazy'>
        </figure>
        <figure class='widescreen'>
            <img src=".loadImg('original', $backdrop)." loading='lazy'>
        </figure>
        <div class='content-wrap mobile-only'>
            <h1 class='h1 text-center'>".$title."</h1>
            <p class='small'>".truncate($description, 450)."</p>
        </div>
        <div class='content-wrap desktop-only'>
            <h1 class='h2'>".$title."</h1>
            <p>".truncate($description, 450)."</p>
        </div>
        <div class='button-wrap'>
            <div class='col-6 col-12-medium grid-padding text-center desktop-only'><a href='/' class='btn btn-small btn-white icon-left icon-info'>Mehr erfahren</a></div>
            <div class='col-12 grid-padding text-center'><a href='/' class='btn btn-small btn-white icon-left icon-play'>Jetzt schauen</a></div>
        </div>
        ";
    
        $conn->close();
        echo '<div class="highlight-wrapper">'.$hightlight.'</div>';
    } 
}

function addHighlight($movieID) {
    $conn = dbConnect();
    $sql = "INSERT INTO highlights(highlight_id, highlight_status) VALUES
    ($movieID, 1)
    ON DUPLICATE KEY UPDATE highlight_status = VALUES(highlight_status)";

    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_trailer_alert');
        page_redirect('/admin/highlights');
    } else {
        $conn->close();
        set_callout('success','update_trailer_success');
        page_redirect('/admin/highlights');
    }
}

function deactivateHighlight($movieID) {
    $conn = dbConnect();
    $sql = "INSERT INTO highlights(movie_id, highlight_status) VALUES
    ($movieID, 0)
    ON DUPLICATE KEY UPDATE highlight_status = VALUES(highlight_status)";

    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_trailer_alert');
        page_redirect('/admin/highlights');
    } else {
        $conn->close();
        set_callout('success','update_trailer_success');
        page_redirect('/admin/highlights');
    }
}

function isHighlight($mediaID) {
    $conn = dbConnect();
    $sql = "SELECT highlight_status FROM highlights WHERE highlight_id=$mediaID";
    $result = $conn->query($sql);

    if ( $result->num_rows > 0) {
        if ( $result->fetch_assoc()['highlight_status'] ) {
            $conn->close();
            return true;
        }

        $conn->close();
        return false;
    }

    $conn->close();
    return false;
}

function deleteHighlight($mediaID) {
    $conn = dbConnect();
    $sql = "DELETE FROM highlights WHERE highlight_id=$mediaID";
   
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        //set_callout('alert','update_trailer_alert');
        page_redirect('/admin/highlights');
    } else {
        $conn->close();
        //set_callout('success','update_trailer_success');
        page_redirect('/admin/highlights');
    }
}