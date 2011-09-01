<!-- CONFIRMATION -->
<br/>

<div align="center">    
    <h2><?php echo _('Endpoints'); ?></h2>
</div>

<table id="confirmation_endpoints" class="withoutBorder">
    <tr>
        <td style="width: 5%; vertical-align: top">
            
        </td>
        <td style="width: 45%; padding-right: 15px; vertical-align: top">
            <div id="view_map_canvas" style="width:100%; height:235px;"></div>        
        </td>
        <td style="width: 45%; padding-left: 15px; vertical-align: top">
            <?php $this->addElement('view_flow', $flow); ?>
            <br/>
            <?php $this->addElement('view_bandwidth'); ?>
            <br/>
            <?php $this->addElement('view_timer', $timer); ?>
        </td>
        <td style="width: 5%; vertical-align: top">      
        </td>
    </tr>
</table>

<div style="height: 25%"></div>

<div class="control_tab">    
    <input type="submit" id="bf"  class="ok" value="<?php echo _('Finished'); ?>"/>
    <input type="button" id="bp3" class="back" value="<?php echo _('Previous'); ?>" onClick="prevTab(this);"/>
    <input type="button" id="bc3" style="float: right" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"/>
</div>
