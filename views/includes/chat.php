<div id="chat">    
    <div class="chat-wrap grid-padding">
        <p id="chat-headline" class="text-center pad-top-xs pad-bottom-xs"><button id="chat-close" class="icon icon-close"></button>Chat</p>
        <div id="message-wrap">

        </div>
        <div id="input-wrap" class="pad-top-xs marg-top-xs marg-bottom-xs">
            <label for="message-input" class="marg-right-xs"><?php echo lang_snippet('message'); ?>
                <input type="text" id="message-input" name="message-input" class="marg-no">
            </label>
            <input type="number" style="display:none;" id="message-use-id" name="message-use-id" value="<?php echo $_SESSION['userID'];?>">
            <input type="text" style="display:none;" id="message-use-name" name="message-use-name" value="<?php echo $_SESSION['username'];?>">
            <button class="btn btn-small btn-white marg-no" id="chatMSG"><?php echo lang_snippet('send'); ?></button>
        </div>
    </div>
</div>