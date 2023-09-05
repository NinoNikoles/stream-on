<?php
$conn = dbConnect();

$newImg = $_POST['img'];
$userID = $_POST['userID'];

$sql = "UPDATE users SET user_img='$newImg' WHERE id=".$userID.";";
if ($conn->query($sql) === TRUE) {
    set_callout('success','user_img_update_success');
    page_redirect("/user/?id=".$userID);
} else {
    set_callout('alert','user_img_update_alert');
    page_redirect("/user/?id=".$userID);
}