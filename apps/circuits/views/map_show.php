<?php

$args = $this->passedArgs;
$bandwidthTip = $args->bandwidthTip;
$res_wizard = $args->res_wizard;

?>

<input type="button" class="advancedview" value="<?php echo _('Advanced View')?>" onclick="redir('<?php echo $this->buildLink(array('controller'=>'flows','action'=>'add_form')) ?>');">

<h1><center><?php echo _("Add Flow - Map View"); ?></center></h1>

<?php if ($res_wizard): ?>
    <h2><?php echo _("Reservation Creation Wizard"); ?></h2>
<?php endif; ?>


    <table cellspacing="10" cellpadding="3">
        <tr>
            <th>
                <?php echo _("Flow name"); ?>
            </th>
            <td colspan="2" align="left">
                <input type="text" size="70" id="name" title="<?php echo _("Set the flow name"); ?>"/>
            </td>
        </tr>
    </table>

<div>
    <div align="center" id="map_canvas" style="float: left; width: 652px; height: 370px;">
    </div>
</div>

<div style="clear: both">
    <table>
        <tr>
            <td align="left">
                <input type="button" class="zoom" value="<?php echo _("Reset Zoom") ?>" onClick="resetZoom();">
            </td>
            <td align="center">
                <input type="button" class="clear" value="<?php echo _("Clear") ?>" onClick="edit_clearAll();">
            </td>
            <td align="right">
                <input type="button" style="display: none" class="invert" value="<?php echo _(" Invert Route") ?>" onClick="invertPath();">
            </td>
        </tr>

        <tr>
            <th>
                
            </th>
            <th>
                <label id="src"><?php echo _(Source);?>:</label>
            </th>
            <th>
                <label id="dst"><?php echo _(Destination);?>:</label>
            </th>
        </tr>
        <tr>
            <th>
                <?php echo _(Network);?>
            </th>
            <td>
                <label id="src_network"></label>
            </td>
            <td>
                <label id="dst_network"></label>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo _(Domain);?>
            </th>
            <td>
                <label id="src_domain"></label>
            </td>
            <td>
                <label id="dst_domain"></label>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo _(Device);?>
            </th>
            <td>
                <select id="src_device" style="display:none" onchange="map_changeDevice('src');"></select>
            </td>
            <td>
                <select id="dst_device" style="display:none" onchange="map_changeDevice('dst');"></select>
                
            </td>
        </tr>
        <tr>
            <th>
                <?php echo _(Port);?>
            </th>
            <td>
                <select id="src_port" style="display:none" onchange="map_changePort('src');"></select>
            </td>
            <td>
                <select id="dst_port" style="display:none" onchange="map_changePort('dst');"></select>
            </td>
        </tr>
    </table>
    
    <?php $this->addElement('vlan'); ?>
    
    <div id="control" style="clear: both">
        <table>
            <tr>
                <th>
                    <?php echo _("Bandwidth"); ?>
                </th>
                <td colspan="2" align="left">
                    <input type="text" id="bandwidth">
                    <?php echo $bandwidthTip; ?>
                </td>
            </tr>
        </table>
            
        <div class="controls">
            <input type="button" class="save" value="<?php echo _("Save"); ?>" onclick="map_saveFlow();">
            <?php if ($res_wizard): ?>
                <input type="button" class="cancel" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'reservations', 'action' => 'page1')); ?>')">
            <?php else: ?>
                <input type="button" class="cancel" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'flows', 'action' => 'show')); ?>')">
            <?php endif; ?>
        </div>
        
    </div>
    
</div>
    
    <div style="display: none">
            <label id="position_origin"> </label>
            <label id="position_destination"></label>
    </div>
