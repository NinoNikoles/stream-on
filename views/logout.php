<?php
    set_callout('success','logout_message');

    session_unset();
    session_destroy();
    session_write_close();
    page_redirect("/");
?>