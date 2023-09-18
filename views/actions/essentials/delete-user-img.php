<?php
$conn = dbConnect();

$img = $_POST['img'];
$imgPath = ROOT_PATH.'/uploads/'.userNameStringFormatter($_SESSION['username']).'/';

// Get uploads array of user
$sql = "SELECT uploads FROM users WHERE id=".$_SESSION['userID'].";";
$uploadsReslut = $conn->query($sql);
if ( $uploadsReslut->num_rows > 0 ) {
    while ( $uploads = $uploadsReslut->fetch_assoc() ) {
        $images = json_decode($uploads['uploads']);
        $newUploadArray = [];
    }
}

foreach ( $images as $image ) {
    if ( !($image === $img) ) {
        $newUploadArray[] = $image;
    }
}

$uploads = json_encode($newUploadArray);

//Delete image file
unlink($imgPath.$img);

// Update image and uploads of User
$sql = "UPDATE users
SET user_img=NULL, uploads='$uploads'
WHERE id=".$_SESSION['userID'].";";

if ($conn->query($sql) === TRUE) {
    set_callout('success',lang_snippet('user_img_update_success'));
} else {
    set_callout('alert',lang_snippet('user_img_update_alert'));
}