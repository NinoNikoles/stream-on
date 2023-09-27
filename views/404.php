<?php
$pageTitle = pageTitle('404');
include(ROOT_PATH.'/views/header.php');

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<div class="col12">';
        echo '<h1>'.lang_snippet('page_not_found').'</h1>';
        echo '<p>';
            echo '<a href="/" class="btn btn-primary icon-left btn-back" title="'.lang_snippet('back_to_homepage').'">'.lang_snippet('back_to_homepage').'</a>';
        echo '</p>';
    echo '</div>';
echo '</div>';

include('footer.php');