<div class="col2 sidebar">
    <ul>
        <li><a href="/settings"><?php echo lang_snippet('Settings'); ?></a></li>
        <?php
            if ($cnf['apikey'] !== NULL || $cnf['apikey'] !== '') {
                echo '<li><a href="/users">'.lang_snippet('Users').'</a></li>';
                echo '<li><a href="/movies">'.lang_snippet('Movies').'</a></li>';
                echo '<li><a href="/shows">'.lang_snippet('Shows').'</a></li>';
                echo '<li><a href="/genres">'.lang_snippet('Genres').'</a></li>';
            }
        ?>
    </ul>
</div>