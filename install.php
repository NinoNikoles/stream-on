<?php
require_once ROOT_PATH.'/views/head.php';

if ( file_exists( ROOT_PATH.'/config.php') ) {
    $servername = DB_HOST;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dbname = DB_NAME;
    $charset = DB_CHARSET;
    $collate = DB_COLLATE;

    if ( !databaseExists($servername, $username, $password, $dbname) ) {
        createDatabase($servername, $username, $password, $dbname, $charset, $collate);
        page_redirect("/");

    } else if ( isset($_POST['table-submit']) ) {
        $pageTitle = $_POST['page-title'];
        $adminUsername = $_POST['user'];
        $adminPassword = $_POST['password'];

        createTables($pageTitle, $adminUsername, $adminPassword, $apikey, $pageLang);
        page_redirect("/");

    } else {
        if ( !tablesExists($servername, $username, $password, $dbname) ) {
        ?>

            <div class="innerWrap">
                <div class="col8 marg-left-col2">
                    <form method="post" action="/install">
                        <div class="col12">
                            <label for="host">Page Title
                                <input type="text" name="page-title" id="page-title" required>
                            </label>
                        </div>
                        <div class="col12">
                            <label for="user">Username
                                <input type="text" name="user" id="user" required>
                            </label>
                        </div>
                        <div class="col12">
                            <label for="password">Password
                                <input type="text" name="password" id="password" required>
                            </label>
                        </div>
                        <div class="col12 text-right">
                            <button type="submit" name="table-submit" id="table-submit" class="btn-success marg-top-s">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        
        <?php
        }
    }
} else {
    new mysqli('localhost', 'root', '');

    if ( isset($_POST['db-submit']) ) {
        $servername = $_POST['host'];
        $username = $_POST['user'];
        $password = $_POST['password'];
        $dbname = $_POST['database'];

        createConfig($servername, $username, $password, $dbname);

        require_once ROOT_PATH.'/config.php';
        $charset = DB_CHARSET;
        $collate = DB_COLLATE;

        createDatabase($servername, $username, $password, $dbname, $charset, $collate);
    }
?>

    <div class="innerWrap">
        <div class="col8 marg-left-col2">
            <form method="post" action="/install">
                <div class="col12">
                    <label for="host">Host
                        <input type="text" name="host" id="host">
                    </label>
                </div>
                <div class="col12">
                    <label for="user">Username
                        <input type="text" name="user" id="user">
                    </label>
                </div>
                <div class="col12">
                    <label for="password">Password
                        <input type="text" name="password" id="password">
                    </label>
                </div>
                <div class="col12">
                    <label for="database">Database
                        <input type="text" name="database" id="database">
                    </label>
                </div>
                <div class="col12 text-right">
                    <button type="submit" name="db-submit" id="db-submit" class="btn-success marg-top-s">Speichern</button>
                </div>
            </form>
        </div>
    </div>

<?php
}
?>