<?php
if ($type == 'source') {
    $label = _('Source');
    $prefix = 'src';
} else {
    $label = _('Destination');
    $prefix = 'dst';
}
?>

<table class="reservation-point">
    <thead>
        <tr>
            <th colspan="2">
                <div class="ui-state-default ui-corner-all" id="<?= $prefix ?>_clearpath" style="float: right; margin: 0 4px; cursor: pointer;">
                    <span class="ui-icon ui-icon-minusthick" title="<?= _("Clear point") ?>"></span>
                </div>
                <div class="ui-state-default ui-corner-all" id="<?= $prefix ?>_thishost" style="float: right; cursor: pointer;">
                    <span class="ui-icon ui-icon-home" title="<?= _("Select this host") ?>"></span>
                </div>
                <div class="ui-state-default ui-corner-all" id="<?= $prefix ?>_choosehost" style="float: right; margin: 0 4px; cursor: pointer;">
                    <span class="ui-icon ui-icon-search" title="<?= _("Choose host") ?>"></span>
                </div>
                <div style="float: none;">
                    <strong><?php echo $label; ?></strong>
                </div>
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td><strong><?php echo _('Domain'); ?></strong></td>
            <td><label id="<?= $prefix ?>_domain"></label></td>
        </tr>
        <tr>
            <td><strong><?php echo _('Network'); ?></strong></td>
            <td><label id="<?= $prefix ?>_network"></label></td>
        </tr>
        <tr>
            <td><strong><?php echo _('Device'); ?></strong></td>
            <td>
                <select id="<?= $prefix ?>_device" onchange="map_changeDevice('<?= $prefix ?>');" disabled></select>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo _('Port'); ?></strong></td>
            <td>
                <select id="<?= $prefix ?>_port" onchange="map_changePort('<?= $prefix ?>');" disabled></select>
                <input type="hidden" id="<?= $prefix ?>_urn" name="<?= $prefix ?>_urn"/>
            </td>
        </tr>
        <?php $prefixex = $prefix == 'src' ? 'source' : 'destiny'; ?>
        <tr>
            <td><strong><?php echo _('VLAN Type'); ?></strong></td>
            <td>
                <input type="checkbox" name="<?= $prefixex ?>VLANType" id="<?= $prefix ?>_vlanTagged" onchange="map_changeVlanType(this,'<?= $prefix ?>');" disabled class="ui-state-disabled"/>
                <input type="hidden" name="<?= $prefix ?>_vlanType" id="<?= $prefix ?>_vlanType"/>
                <label for="<?= $prefix ?>_vlanTagged"><?php echo _("Tagged"); ?></label>
            </td>
        </tr>
        <tr>
            <td><strong><?php echo _('VLAN'); ?></strong></td>
            <td class="vlan-input">
                <input type="text" id="<?= $prefix ?>_vlanText" size="14" name="<?= $prefix ?>_vlan" disabled="disabled" class="ui-state-disabled"/>
            </td>
        </tr>
    </tbody>

</table>
