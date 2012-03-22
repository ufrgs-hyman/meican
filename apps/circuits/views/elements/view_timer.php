<dl>
    <dt><?= _("Start") ?></dt>
    <dd><label id="view_startTimer"><?php if ($timer) echo $timer->start; ?></label></dd>
    <dt><?= _("Finish") ?></dt>
    <dd><label id="view_finishTimer"><?php if ($timer) echo $timer->finish; ?></label></dd>
    <dt><?= _("Duration") ?></dt>
    <dd><label id="view_durationTimer"><?php if ($timer) echo $timer->duration; ?></label></dd>
    
    <?php if (!$timer || $timer->summary): ?>
            <dt>
                <?= _("Summary") ?>
            </dt>
            <dd>
                <label id="confirmation_summary"><?= !empty($timer)&&$timer->summary?$timer->summary:null ; ?></label>
            </dd>
    <?php endif; ?>
</dl>