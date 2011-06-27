<!-- LAYOUT DEFAULT -->

<?php echo $content_for_script; ?>


<?php if ($content_for_flash): ?>
        <div class="flash_box">
        <?php foreach ($content_for_flash as $f) : ?>
            <?php
            $ar = explode(":", $f);
            $status = $ar[0];
            $message = $ar[1];
            ?>
            <div class="<?php echo $status; ?>"><?php echo $message; ?></div>
        <?php endforeach; ?>
        </div>
<?php endif; ?>

<div class="content">
    <?php echo $content_for_body; ?>
</div>