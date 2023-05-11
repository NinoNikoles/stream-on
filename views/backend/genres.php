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

            set_callout('success','genres created alert');
            header('Location: /genres');
            exit();
            echo 'Datensatz erfolgreich hinzugefügt.';
        }
    }

    set_callout('success','genres created success');
    header('Location: /genres');
    exit();
}
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('Genres'); ?></h1>
            </div>
        
            <?php 
                if ($result) {
                    $row = $result->fetch_assoc();
                    $numRows = $row['num_rows'];
                
                    // Überprüfen, ob die Tabelle leer ist
                    if ($numRows == 0) {
                        echo '<div class="col12 marg-bottom-m">';
                            echo '<form method="post" action="/genres">';
                                echo '<button type="submit" name="generate-genres">Genres erstellen</button>';
                            echo '</form>';
                        echo '</div>';
                    }
                }
            ?>

            <div class="col12">
                <?php callout(); ?>

                <table>
                    <thead>
                        <th><?php echo lang_snippet('ID'); ?></th>
                        <th><?php echo lang_snippet('TMDB ID'); ?></th>
                        <th><?php echo lang_snippet('Name'); ?></th>
                    </thead>
                    <?php

                        $sql = "SELECT id, genre_id, genre_name FROM genres";
                        $results = $conn->query($sql);
                        while($row = $results->fetch_assoc()) {
                            echo '<tr>';
                                echo '<td>'.$row['id'].'</td>';
                                echo '<td>'.$row['genre_id'].'</td>';
                                echo '<td>'.$row['genre_name'].'</td>';
                            echo '</tr>';
                        }

                    ?>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>
