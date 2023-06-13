<?php

$conn = $mysqli;
// Benutzerregistrierung
if(isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO user (username, password) VALUES ('$username', '$hashed_password')";
    mysqli_query($conn, $sql);
    page_redirect("/login");
}
?>

<form method="post" action="register">
    <input type="text" name="username" placeholder="Benutzername" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <button type="submit" name="register">Registrieren</button>
</form>
