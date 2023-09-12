<?php
$conn = dbConnect();

$newImg = $_POST['img'];
$userID = $_POST['userID'];

$sql = "UPDATE users SET user_img='$newImg' WHERE id=".$userID.";";
if ($conn->query($sql) === TRUE) {
    set_callout('success',lang_snippet('user_img_update_success'));
} else {
    set_callout('alert',lang_snippet('user_img_update_alert'));
}