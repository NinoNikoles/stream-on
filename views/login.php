<?php
    $conn = dbConnect();
    $pageTitle = pageTitle('Login');
    require_once ROOT_PATH.'/views/head.php';
?>

<div class="innerWrap">
    <div class="col4 marg-left-col4 marg-top-xxl">
        <h1>Login</h1>
        <?php callout(); ?>
        <form>
            <p>
                <label for="username"><?php echo lang_snippet('username'); ?>
                <input type="text" name="username" id="username" placeholder="Benutzername" required></label>
            </p>
            <p>
                <label for="password"><?php echo lang_snippet('password'); ?>
                <input type="password" name="password" id="password" placeholder="Passwort" required></label>
            </p>
            <div class="text-right">
                <button class="btn btn-primary loading" type="submit" id="login" name="login"><?php echo lang_snippet('login'); ?></button>
            </div>
        </form>
    </div>
</div>

<?php
    require_once ROOT_PATH.'/views/footer.php';
?>