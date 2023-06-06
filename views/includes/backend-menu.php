<div class="col2 sidebar">
    <ul>
        <li><a href="/settings"><?php echo lang_snippet('settings'); ?></a></li>
        <?php
            $apikey = get_apikey_db();
            echo $apikey;
            if (!($apikey === NULL)) {
                echo '<li><a href="/users">'.lang_snippet('users').'</a></li>';
                echo '<li><a href="/movies">'.lang_snippet('movies').'</a></li>';
                echo '<li><a href="/shows">'.lang_snippet('shows').'</a></li>';
                echo '<li><a href="/genres">'.lang_snippet('genres').'</a></li>';
            }
        ?>
    </ul>
</div>