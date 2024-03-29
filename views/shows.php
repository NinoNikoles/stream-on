<?php 
$pageTitle = pageTitle(lang_snippet(('shows')));
include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<h1>'.lang_snippet('shows').'</h1>';

    // Filter
    include_filter('show');

    // Media Output
    echo '<div class="grid-row" id="media-list">';
        $shows = selectAllShowsByTitle('ASC');
        foreach ($shows as $show) {
            echo media_card($show, 'col-6 col-4-xsmall col-3-medium grid-padding');
        }
    echo '</div>';
    echo '<p id="loading-message" style="display: none;">Loading...</p>';
echo '</div>';

include(ROOT_PATH.'/views/footer.php');
?>