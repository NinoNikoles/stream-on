<?php
    $conn = dbConnect();
    $pageTitle = pageTitle('Login');

    // Benutzeranmeldung
    if(isset($_POST['login'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        if ( $result->num_rows > 0 ) {
            $row = mysqli_fetch_assoc($result);

            if(password_verify($password, $row['password'])) {
                $_SESSION['userID'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['logged_in'] = true;
    
                page_redirect('/');
            } else {
                echo '<div class="innerWrap">';
                    echo '<div class="col4 marg-left-col4">';
                        echo '<p class="text-alert">Benutzername oder Passwort falsch.';
                    echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="innerWrap">';
                echo '<div class="col4 marg-left-col4">';
                    echo '<p class="text-alert">Benutzername exisitert nicht.';
                echo '</div>';
            echo '</div>';
        }
    }

    require_once ROOT_PATH.'/views/head.php';
?>

<div class="innerWrap">
    <div class="col4 marg-left-col4 marg-top-xxl">
        <?php callout(); ?>
        <form method="post" action="/login">
            <p>
                <label for="username">Benutzername
                <input type="text" name="username" id="username" placeholder="Benutzername" required></label>
            </p>
            <p>
                <label for="password">Passwort
                <input type="password" name="password" id="password" placeholder="Passwort" required></label>
            </p>
            <div class="text-right">
                <button class="btn btn-primary" type="submit" name="login">Anmelden</button>
            </div>
        </form>
    </div>
</div>