<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<h1>'.lang_snippet('my_list').'</h1>';
    echo '<div class="grid-row">';
        myList();
    echo '</div>';
echo '</div>';

include(ROOT_PATH.'/views/footer.php');
?>