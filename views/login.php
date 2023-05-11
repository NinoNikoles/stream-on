<?php 
    include(ROOT_PATH.'/views/head.php');

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Benutzeranmeldung
    if(isset($_POST['login'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        
        if(password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['username'] = $row['username'];
            $_SESSION['logged_in'] = true;
            setcookie('session_id', session_id(), time() + (86400 * 9999), "/");
            setcookie('PHPSESSID', session_id(), time() + (86400 * 9999), "/");
            $sql = 'UPDATE users SET session="'.session_id().'" WHERE username="'.$username.'"';
            if (!($conn->query($sql) === TRUE)) {
                die('Error creating table: ' . $conn->error);
            }
            header('Location: /');
            exit();
        } else {
            echo '<div class="innerWrap">';
                echo '<div class="col4 marg-left-col4">';
                    echo '<p class="text-alert">Benutzername oder Passwort falsch.';
                echo '</div>';
            echo '</div>';
        }
    }
?>

<div class="innerWrap">
    <div class="col4 marg-left-col4 marg-top-xxl">
        <?php callout(); ?>
        <form method="post" action="">
            <p>
                <label for="username">Benutzername
                <input type="text" name="username" placeholder="Benutzername" required></label>
            </p>
            <p>
                <label for="password">Passwort
                <input type="password" name="password" placeholder="Passwort" required></label>
            </p>
            <div class="text-right">
                <button class="btn btn-primary" type="submit" name="login">Anmelden</button>
            </div>
        </form>
    </div>
</div>
