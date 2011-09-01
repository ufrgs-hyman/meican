<?php 
    $bandwidth = $argsToElement;
?>

<table style="width: 100%">
    <tr>
        <th style="width: 30%; border:none">
            <?php echo _("Bandwidth"); ?>
        </th>
        <td>
            <label id="lb_bandwidth"><?php if ($bandwidth)  echo "$bandwidth  Mbps";  ?></label>
        </td>
    </tr>
</table>    