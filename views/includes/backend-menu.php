<div class="col2 sidebar">
    <ul>
        <li><a href="/admin/settings"><?php echo lang_snippet('settings'); ?></a></li>
        <li><a href="/admin/users"><?php echo lang_snippet('users'); ?></a></li>
        <?php
            $apikey = get_apikey_db();
            if (!($apikey === NULL)) {
                //echo '<li><a href="/shows">'.lang_snippet('shows').'</a></li>';
                echo '<li><a href="/admin/genres">'.lang_snippet('genres').'</a></li>';

                $genreCheck = genreCheck();
                if ( $genreCheck === true) {
                    echo '<li><a href="/admin/movies">'.lang_snippet('movies').'</a></li>';
                    echo '<li><a href="/admin/highlight">'.lang_snippet('highlight').'</a></li>';
                }
            }
        ?>
    </ul>
</div>