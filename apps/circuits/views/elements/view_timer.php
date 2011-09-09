<?php

$timer = $argsToElement;

?>

<table style="width: 100%">

    <tr>
        <th>
            <?php echo _("Start") ?>
        </th>
        <th>
            <?php echo _("Finish") ?>
        </th>
        <th>
            <?php echo _("Duration") ?>
        </th>
    </tr>

    <tr>
        <td>
            <label id="view_startTimer"><?php if ($timer) echo $timer->start; ?></label>
        </td>

        <td>
            <label id="view_finishTimer"><?php if ($timer) echo $timer->finish; ?></label>
        </td>

        <td>
            <label id="view_durationTimer"><?php if ($timer) echo $timer->duration; ?></label>
        </td>
    </tr>

    <?php if ($timer): ?>
        <?php if($timer->summary): ?>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <th colspan="3">
                <?php echo _("Summary") ?>
            </th>
        </tr>
        <tr>
            <td colspan="3">
                <label id="confirmation_summary"><?php echo $timer->summary ; ?></label>
            </td>
        </tr>
        <?php endif; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <th colspan="3">
                <?php echo _("Summary") ?>
            </th>
        </tr>
        <tr>
            <td colspan="3">
                <label id="confirmation_summary"></label>
            </td>
        </tr>
    <?php endif; ?>

</table>