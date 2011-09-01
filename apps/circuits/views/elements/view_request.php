<?php

$request = $argsToElement;

?>

<table class="list">
     <tr>
        <th>
            <?php echo _('Status'); ?>
        </th>
        <td>
            <?php echo $request->status; ?>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo _('Response'); ?>
        </th>
        <td>
            <?php echo $request->response; ?>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo _('Message'); ?>
        </th>
        <td>
            <?php echo $request->message; ?>
        </td>
    </tr>
</table>