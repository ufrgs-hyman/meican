<?php
if ($type == 'source') {
    $label = _('Source');
    $prefix = 'src';
} else {
    $label = _('Destination');
    $prefix = 'dst';
    $type = 'dest';
}
?>

<table class="reservation-point view-point">
    <thead>
        <tr>
            <th colspan="2">
    <div style="float: none;">
        <strong><?php echo $label; ?></strong>
    </div>
</th>
</tr>
</thead>
<tbody>
    <tr>
        <td><strong><?php echo _('Domain'); ?></strong></td>
        <td>
            <label id="confirmation_<?= $prefix ?>_domain"><?php if ($flow)
    echo $flow->{$type}->domain; ?></label>
        </td>
    </tr>
    <tr>
        <td><strong><?php echo _('Network'); ?></strong></td>
        <td>
            <label id="confirmation_<?= $prefix ?>_network"><?php if ($flow)
    echo $flow->{$type}->network; ?></label>
        </td>
    </tr>
    <tr>
        <td><strong><?php echo _('Device'); ?></strong></td>
        <td>
            <label id="confirmation_<?= $prefix ?>_device"><?php if ($flow)
    echo $flow->{$type}->device; ?></label>
        </td>
    </tr>
    <tr>
        <td><strong><?php echo _('Port'); ?></strong></td>
        <td>
            <label id="confirmation_<?= $prefix ?>_port"><?php if ($flow)
        echo $flow->{$type}->port; ?></label>
        </td>
    </tr>
                <?php $prefixex = $prefix == 'src' ? 'source' : 'destiny'; ?>
    <tr>
        <td><strong><?php echo _('VLAN Type'); ?></strong></td>
        <td>
            <label id="confirmation_<?= $prefix ?>_vlan">
                <?php
                if ($flow) {
                    if ($flow->{$type}->vlan === 0)
                        $vlan = "Untagged";
                    else
                        $vlan = ($flow->{$type}->vlan === NULL) ? "Tagged: " . _("any") : "Tagged: " . $flow->{$type}->vlan;
                    echo $vlan;
                }
                ?>
            </label>
        </td>
    </tr>
    <?php /* <tr>
      <td><strong><?php echo _('VLAN'); ?></strong></td>
      <td class="vlan-input">
      <input type="text" id="<?= $prefix ?>_vlanText" size="14" name="<?= $prefix ?>_vlan" disabled="disabled" class="ui-state-disabled"/>
      </td>
      </tr> */ ?>
</tbody>
</table>
