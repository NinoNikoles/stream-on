<?php
$conn = dbConnect();
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['psswd']);

$sql = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $sql);
if ( $result->num_rows > 0 ) {
    $row = mysqli_fetch_assoc($result);

    if(password_verify($password, $row['password'])) {
        $_SESSION['userID'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['logged_in'] = true;

        ajaxPage_redirect('/');
    } else {
        set_callout('alert', lang_snippet('username_pw_wrong'));
    }
} else {
    set_callout('warning', lang_snippet('username_not_found'));
}
?>