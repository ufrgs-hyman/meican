<!-- LAYOUT DEFAULT -->

<?php if ($this->script->jsFiles): ?>
<div class="scripts">
    <?php foreach ($this->script->jsFiles as $f) {
        echo "<i>".Dispatcher::getInstance()->url('').$f."</i>";
    } ?>
</div>
<?php endif; ?>
<?php if ($this->script->scriptArgs): ?>
<script>
<?php
    foreach ($this->script->scriptArgs as $name => $val) {
        if (is_string($val))
            echo "var $name = '$val';";
        elseif (is_array($val) || is_object($val))
            echo "var $name = ".json_encode($val).";";
        else
            echo "var $name = $val;";
    }?>
</script>
<?php endif; ?>


<?php if ($content_for_flash): ?>
        <div class="flash_box">
        <?php foreach ($content_for_flash as $f) : ?>
            <?php
            $ar = explode(":", $f);
            $status = $ar[0];
            $message = $ar[1];
            ?>
            <div class="<?php echo $status; ?>"><?php echo $message; ?>
                <input type="button" class="closeFlash" onclick="clearFlash();"/>
            </div>
        <?php endforeach; ?>
        </div>
<?php endif; ?>

<?php echo $content_for_body; ?>
