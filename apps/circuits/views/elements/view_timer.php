<?php

$timer = $argsToElement;

?>

<table>

    <tr>
        <th colspan="2">
            <?php echo _("Start") ?>
        </th>
        <th colspan="2">
            <?php echo _("Finish") ?>
        </th>
        <th>
            <?php echo _("Duration") ?>
        </th>
    </tr>

    <tr>
        <td>
            <label id="confirmation_initialDate"><?php if ($timer) echo $timer->start; ?></label>
        </td>
        <td>
            <label id="confirmation_initialTime"></label>
        </td>
        <td>
            <label id="confirmation_finalDate"><?php if ($timer) echo $timer->finish; ?></label>
        </td>
        <td>            
            <label id="confirmation_finalTime"></label>
        </td>
        <td>
            <label id="confirmation_duration"><?php if ($timer) echo $timer->duration; ?></label>
        </td>
    </tr>

    <?php if ($timer): ?>
        <?php if($timer->summary): ?>
        <tr>
            <th colspan="5">
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
            <th colspan="5">
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