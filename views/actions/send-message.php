<?php

$userID = $_POST['userID'];
$username = $_POST['username'];

if ( isset($_POST['joined']) ) {
    echo '
    <div class="message joint-msg marg-bottom-xs">
        <span class="imgWrap">
            <figure class="square">
                <img src="'.userProfileImg($userID).'">
            </figure>
        </span>    
        <div class="message-content-wrap">
            <span class="message-text marg-left-xs small"><strong>'.$username.'</strong> '.lang_snippet('has_joined_the_chat').'</span>
        </div>
    </div>';
} else {
    $message = $_POST['message'];

    $msgClass = '';
    
    if ( $userID === $_SESSION['userID'] ) {
        $msgClass = 'self';  
    }

    echo '
    <div class="message '.$msgClass.' marg-bottom-xs">
        <div class="message-content-wrap">
            <p class="message-username marg-bottom-no strong">'.$username.'</p>
            <p class="message-text small marg-bottom-no">'.$message.'</p>
        </div>
        <span class="imgWrap marg-left-xs">
            <figure class="square">
                <img src="'.userProfileImg($userID).'">
            </figure>
        </span>
    </div>';
}
