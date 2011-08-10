<?php

$fed_descr = isset($argsToElement->fed_descr) ? $argsToElement->fed_descr : NULL;
$fed_ip = isset($argsToElement->fed_ip) ? $argsToElement->fed_ip : NULL;

?>

<table>
    <tr>
        <th>
            <?php echo _("Name"); ?>:
        </th>
        <td>
            <input type="text" name="fed_descr" size="50" value="<?php echo $fed_descr; ?>"/>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo _("Federation IP"); ?>:
        </th>
        <td>
            <input type="text" name="fed_ip" size="50" value="<?php echo $fed_ip; ?>"/>
        </td>
    </tr>
</table>