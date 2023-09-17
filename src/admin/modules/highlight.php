<?php

function getHighlight() {
    $conn = dbConnect();
    $hightlightSelect = "SELECT highlights.highlight_id, media.title, media.overview, media.poster, media.backdrop, media.mediaType, media.trailer
    FROM highlights INNER JOIN media ON highlights.highlight_id = media.tmdbID
    WHERE highlights.highlight_status = 1
    ORDER BY RAND() LIMIT 1";
    $hightlightResult = $conn->query($hightlightSelect);

    if ( $hightlightResult->num_rows > 0) {
        while ( $highlight = $hightlightResult->fetch_assoc() ) {
            $mediaID = $highlight['highlight_id'];
            $title = $highlight['title'];
            $description = $highlight['overview'];
            $poster = $highlight['poster'];
            $backdrop = $highlight['backdrop'];
            $trailerID = $highlight['trailer'];
        }

        $trailer = "";
        $style = "";
        if ( !($trailerID === NULL) ) {
            $trailer = '<iframe id="highlightTrailer" type="text/html" src="http://www.youtube.com/embed/'.$trailerID.'?enablejsapi=1&origin='.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'&autoplay=1&controls=0&modestbranding=1&iv_load_policy=3&showinfo=0&rel=0" style="height:100vh!important;position:absolute;" frameborder="0"></iframe>';
            $style = "style='opacity:0; transition:opacity 0.3s ease-in;'";
        }

        $hightlight = "
        <figure class='poster'>
            <img data-img=".loadImg('original', $poster)." loading='lazy' alt='".$title."'>
        </figure>
        <figure class='widescreen'>
            <img data-img=".loadImg('original', $backdrop)." loading='lazy' alt='".$title."'>
            ".$trailer."
        </figure>
        <div class='content-wrap mobile-only'>
            <h1 class='h1 text-center'>".$title."</h1>
            <p class='small'>".truncate($description, 450)."</p>
        </div>
        <div class='content-wrap desktop-only' ".$style.">
            <h1 class='h2'>".$title."</h1>
            <p>".$description."</p>
        </div>
        <div class='button-wrap'>
            <div class='col-6 col-12-medium grid-padding text-center desktop-only'><a href='#content-$mediaID' class='btn btn-small btn-white icon-left icon-info info-trigger' data-modal data-src='$mediaID'>Mehr erfahren</a></div>
        </div>
        ";

        //<div class='col-12 grid-padding text-center'><a href='/' class='btn btn-small btn-white icon-left icon-play'>Jetzt schauen</a></div>
    
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
        set_callout('alert','add_highlight_alert');
        page_redirect('/admin/highlights');
    } else {
        $conn->close();
        set_callout('success','add_highlight_success');
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
        page_redirect('/admin/highlights');
    } else {
        $conn->close();
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
    }

    $conn->close();
    return false;
}

function deleteHighlight($mediaID) {
    $conn = dbConnect();
    $sql = "DELETE FROM highlights WHERE highlight_id=$mediaID";
   
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','delete_highlight_alert');
        page_redirect('/admin/highlights');
    } else {
        $conn->close();
        set_callout('success','delete_highlight_success');
        page_redirect('/admin/highlights');
    }
}