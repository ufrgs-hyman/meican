<?php

$timer = $argsToElement;

?>

<table>

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
            <?php echo $timer->start; ?>
        </td>
        <td>
            <?php echo $timer->finish; ?>
        </td>
        <td>
            <?php echo $timer->duration; ?>
        </td>
    </tr>

    <?php if ($timer->summary != '-'): ?>
    <tr>
        <th colspan="3">
            <?php echo _("Recurrence") ?>
        </th>
    </tr>

    <tr>
        <td colspan="3">
            <?php echo $timer->summary; ?>
        </td>
    </tr>
    <?php endif; ?>

</table>