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
            <label id="confirmation_initialDate"></label>
        </td>
        <td>
            <label id="confirmation_initialTime"></label>
        </td>
        <td>
            <label id="confirmation_finalDate"></label>
        </td>
        <td>            
            <label id="confirmation_finalTime"></label>
        </td>
        <td>
            <label id="confirmation_duration"></label>
        </td>
    </tr>

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

</table>