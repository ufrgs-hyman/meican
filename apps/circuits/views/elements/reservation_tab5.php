<!-- CONFIRMATION -->
<br/>

<table class="withoutBorder">
    <tr>
        <td style="width:1%"></td>
        <td class="left" style="color:black"><h1><?php echo _('Confirmation'); ?></h1></td>
    </tr>
</table>

<br/>
<table id="confirmation_endpoints" class="withoutBorder">
    <tr>
        <td style="width: 1%; vertical-align: top"></td>
        <td style="width: 60%; padding-right: 5px; vertical-align: top">
            <div id="view_map_canvas" style="width:100%; height:400px;"></div>        
        </td>
        <td style="width: 38%; padding-left: 5px; vertical-align: top">
            <?php $this->addElement('view_flow', $flow); ?>
            <br/>
            <?php $this->addElement('view_bandwidth'); ?>
            <br/>
            <?php $this->addElement('view_timer', $timer); ?>
        </td>
        <td style="width: 1%; vertical-align: top"></td>
    </tr>
</table>

<div style="height: 6%"></div>

<div class="control_tab">    
    <input type="submit" id="bf"  class="ok" value="<?php echo _('Finished'); ?>"/>
    <input type="button" id="bp3" class="back" value="<?php echo _('Previous'); ?>" onClick="prevTab(this);"/>
    <input type="button" id="bc3" style="float: right" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"/>
</div>
