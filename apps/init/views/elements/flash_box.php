<?php if (!empty($content_for_flash)): ?>
    <?php foreach ($content_for_flash as $f) : ?>
        <?php
        $ar = explode(":", $f);
        $status = $ar[0];
        $message = $ar[1];
        ?>
        <div class="<?php echo $status; ?> ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-closethick close-button" onclick="clearFlash();"></span><?php echo $message; ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
