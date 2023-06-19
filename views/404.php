<?php
include(ROOT_PATH.'/views/header.php');

if( $_SESSION > 0 && !isset($_SESSION['logged_in']) || $_SESSION > 0 && $_SESSION['logged_in'] !== true ) {
    page_redirect("/login");
} else {
    echo '<div class="innerWrap marg-top-l">';
        echo '<div class="col12">';
            echo '<h1>PAGE NOT FOUND</h1>';
            echo '<p>';
                echo '<a href="/" class="btn btn-primary icon-left btn-back" title="Zurück zur Startseite">Zurück zur Startseite</a>';
            echo '</p>';
        echo '</div>';
    echo '</div>';
}

include('footer.php');