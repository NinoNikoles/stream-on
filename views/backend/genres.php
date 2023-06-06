<?php
    include(ROOT_PATH.'/views/head.php');
    include(ROOT_PATH.'/views/header.php');

$conn = $mysqli;
$sql = 'SELECT COUNT(*) as num_rows FROM genres';
$result = $conn->query($sql);

if(isset($_POST['generate-genres'])) {
    $genres = $tmdb->getGenres();
    foreach ($genres as $genre) {
        $createGenre = 'INSERT INTO genres (genre_id, genre_name) VALUES ("'.$genre->getID().'", "'.$genre->getName().'")';
        $insertResult = $conn->query($createGenre);
        if (!$insertResult) {

            set_callout('alert','genres_created_alert');
            header('Location: /genres');
            exit();
        }
    }

    set_callout('success','genres_created_success');
    header('Location: /genres');
    exit();
}
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('genres'); ?></h1>
            </div>
        
            <?php 
                if ($result) {
                    $row = $result->fetch_assoc();
                    $numRows = $row['num_rows'];
                
                    // Überprüfen, ob die Tabelle leer ist
                    if ($numRows == 0) {
                        echo '<div class="col12 marg-bottom-m">';
                            echo '<form method="post" action="/genres">';
                                echo '<button type="submit" name="generate-genres">'.lang_snippet('genres').'</button>';
                            echo '</form>';
                        echo '</div>';
                    }
                }
            ?>

            <div class="col12">
                <?php callout(); ?>

                <table>
                    <thead>
                        <th><?php echo lang_snippet('id'); ?></th>
                        <th><?php echo lang_snippet('tmdb_id'); ?></th>
                        <th><?php echo lang_snippet('name'); ?></th>
                    </thead>
                    <?php

                        $sql = "SELECT id, genre_id FROM genres ORDER BY id";
                        $results = $conn->query($sql);
                        while($row = $results->fetch_assoc()) {
                            $genres = $tmdb->getGenres();
                            foreach ($genres as $genre) {
                                if ($genre->getID() == $row['genre_id']) {
                                    $genreName = $genre->getName();
                                    echo '<tr>';
                                        echo '<td>'.$row['id'].'</td>';
                                        echo '<td>'.$row['genre_id'].'</td>';
                                        echo '<td>'.$genreName.'</td>';
                                    echo '</tr>';
                                }
                            }
                        }

                    ?>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>

