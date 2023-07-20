<?php




function getHighlight() {
    $conn = dbConnect();
    $hightlightSelect = "SELECT highlights.highlight_id, media.title, media.overview, media.poster, media.backdrop, media.mediaType
    FROM highlights INNER JOIN media ON highlights.highlight_id = media.tmdbID
    WHERE highlights.highlight_status = 1
    ORDER BY RAND() LIMIT 1";
    $hightlightResult = $conn->query($hightlightSelect);

    if ( $hightlightResult->num_rows > 0) {
        while ( $highlight = $hightlightResult->fetch_assoc() ) {
            $title = $highlight['title'];
            $description = $highlight['overview'];
            $poster = $highlight['poster'];
            $backdrop = $highlight['backdrop'];
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

function addHighlight($mediaID) {
    $conn = dbConnect();
    $sql = "INSERT INTO highlights(highlight_id, highlight_status) VALUES
    ($mediaID, 1)
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

function deactivateHighlight($mediaID) {
    $conn = dbConnect();
    $sql = "INSERT INTO highlights(movie_id, highlight_status) VALUES
    ($mediaID, 0)
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