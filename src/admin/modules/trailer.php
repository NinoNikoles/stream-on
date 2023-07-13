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
    $sql = "SELECT movie_tmdbID, movie_title, movie_overview, movie_poster, movie_thumbnail FROM movies INNER JOIN highlights ON movies.movie_tmdbID=highlights.movie_id WHERE highlights.highlight_status=1 ORDER BY RAND() LIMIT 1";
    if ( $conn->query($sql)->num_rows > 0) {
        $result = $conn->query($sql);

        while ( $row = $result->fetch_assoc() ) {
            $movieID = $row['movie_tmdbID'];
            $title = $row['movie_title'];
            $description = $row['movie_overview'];
            $poster = $row['movie_poster'];
            $backdrop = $row['movie_thumbnail'];
        }

        $hightlight = "
        <figure class='poster'>
            <img src=".loadImg('original', $poster)." loading='lazy'>
        </figure>
        <figure class='widescreen'>
            <img src=".loadImg('original', $backdrop)." loading='lazy'>
        </figure>
        <div class='content-wrap mobile-only'>
            <h1 class='h6'>".$title."</h1>
            <p class='smaller'>".truncate($description)."</p>
        </div>
        <div class='content-wrap desktop-only'>
            <h1 class='h2'>".$title."</h1>
            <p>".$description."</p>
        </div>
        ";
    
        $conn->close();
        echo '<div class="highlight-wrapper">'.$hightlight.'</div>';
    }  
}

function addHighlight($movieID) {
    $conn = dbConnect();
    $sql = "INSERT INTO highlights(movie_id, highlight_status) VALUES
    ($movieID, 1)
    ON DUPLICATE KEY UPDATE highlight_status = VALUES(highlight_status)";

    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_trailer_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
        $conn->close();
        set_callout('success','update_trailer_success');
        page_redirect('/admin/movie/?id='.$movieID);
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

function isHighlight($movieID) {
    $conn = dbConnect();
    $sql = "SELECT highlight_status FROM highlights WHERE movie_id=$movieID";
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

function deleteHighlight($movieID) {
    $conn = dbConnect();
    $sql = "DELETE FROM highlights WHERE movie_id=$movieID";
   
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