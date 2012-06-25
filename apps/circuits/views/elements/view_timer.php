<div>
<div class="label-description"><?php echo _("Start") ?>: </div>
<label><?php if ($timer) echo $timer->start; ?></label>
<div class="label-description"><?php echo _("Finish") ?>: </div>
<label><?php if ($timer) echo $timer->finish; ?></label>
<div class="label-description"><?php echo _("Duration"); ?>: </div>
<label><?php if ($timer) echo $timer->duration; ?></label>

</div> 
<?php if (!$timer || $timer->summary): ?>
    <div>
        <div class="label-description"><<?php echo _("Summary"); ?></div>
        <label><?= !empty($timer) && $timer->summary ? $timer->summary : null; ?></label>
    </div>
<?php endif; ?>