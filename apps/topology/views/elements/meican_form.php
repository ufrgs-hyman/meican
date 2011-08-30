<?php

$meican_descr = isset($argsToElement->meican_descr) ? $argsToElement->meican_descr : NULL;
$meican_ip = isset($argsToElement->meican_ip) ? $argsToElement->meican_ip : NULL;
$meican_dir_name = isset($argsToElement->meican_dir_name) ? $argsToElement->meican_dir_name : NULL;
$is_local_domain = isset($argsToElement->local_domain) ? $argsToElement->local_domain : FALSE;

?>

<table class="withoutBorder add">
    <tr>
        <th class="right">
            <?php echo _("Name"); ?>:
        </th>
        <td class="left">
            <input type="text" name="meican_descr" size="30" value="<?php echo $meican_descr; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("MEICAN IP"); ?>:
        </th>
        <td class="left">
            <input type="text" name="meican_ip" size="30" value="<?php echo $meican_ip; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("Directory name"); ?>:
        </th>
        <td class="left">
            <input type="text" name="meican_dir_name" size="30" value="<?php echo $meican_dir_name; ?>"/>
        </td>
    </tr>
    <tr>
        <th class="right">
            <?php echo _("Is local domain?"); ?>
        </th>
        <td class="left">
            <input type="checkbox" name="local_domain" <?php if ($is_local_domain) echo 'checked="true"'; ?>/>
        </td>
    </tr>
</table>