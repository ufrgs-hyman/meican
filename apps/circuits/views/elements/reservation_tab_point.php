<?php
if ($type == 'source') {
    $label = _(Source);
    $prefix = 'src';
} else {
    $label = _(Destination);
    $prefix = 'dst';
}
?>

<table border="0" cellpadding="3" cellspacing="1" bgcolor="#BBB" style="width: 180px; box-shadow: 2px 2px 4px #888; height:120px;">
    <tbody><tr bgcolor="#DDDDDD" align="center">
            <td colspan="2">
                <div style="float: right; border-style:solid; border-width:thin; border-color: #BBB; margin: 0; padding: 0 3px;">×</div>
                <div style="float: none;"><strong><?php echo $label; ?></strong></div>
            </td>
        </tr>
        <tr bgcolor="#FFFFFF" align="center">
            <td><strong><?php echo _(Domain); ?></strong></td>
            <td><label id="<?= $prefix ?>_domain"></label></td>
        </tr>
        <tr bgcolor="#FFFFFF" align="center">
            <td><strong><?php echo _(Network); ?></strong></td>
            <td><label id="<?= $prefix ?>_network"></label></td>
        </tr>
        <tr bgcolor="#FFFFFF" align="center">
            <td><strong><?php echo _(Device); ?></strong></td>
            <td class="pad">
                <select id="<?= $prefix ?>_device" style="display:none" onchange="map_changeDevice('<?= $prefix ?>');"></select>
            </td>
        </tr>
        <tr bgcolor="#FFFFFF" align="center">
            <td><strong><?php echo _(Port); ?></strong></td>
            <td class="pad">
                <select id="<?= $prefix ?>_port" style="display:none" onchange="map_changePort('<?= $prefix ?>');"></select>
                <input type="hidden" id="<?= $prefix ?>_urn" name="<?= $prefix ?>_urn"/>
            </td>
</tr>
<!-- <tr bgcolor="#FFFFFF" align="center">
    <td><strong><?php echo _(Type); ?></strong></td>
    <td><input type="checkbox" name="checkbox" id="checkbox" checked="">
        <label for="checkbox">Tagged</label></td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
    <td><strong><?php echo _(VLAN); ?></strong></td>
    <td>
        <input name="textfield" type="text" id="textfield" value="3800" style="width:50px; text-align:center;">
        <br>
        3800 ~ 3899</td>
</tr> -->
</tbody>
</table>
