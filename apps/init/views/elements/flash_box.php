<?php if (empty($content_for_flash))
    $content_for_flash = array(); ?>

<div id="flash_box" class="ui-widget">
    <?php foreach ($content_for_flash as $f) : ?>
        <?php
        $ar = explode(":", $f);
        $status = $ar[0];
        $message = $ar[1];
        ?>
        <div class="<?php echo $status; ?>">
            <p><span class="ui-icon ui-icon-closethick close-button" onclick="clearFlash();"></span><?php echo $message; ?></p>
        </div>
    <?php endforeach; //When changing this code, remember to also change main.js flash?>
</div>