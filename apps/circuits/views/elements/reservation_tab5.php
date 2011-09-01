<!-- CONFIRMATION -->
<br/>

<div align="center">    
    <h2><?php echo _('Endpoints'); ?></h2>
</div>

<table id="confirmation_endpoints" class="withoutBorder">
    <tr>
        <td style="width: 15%">
            
        </td>
        <td style="width: 30%; padding-right: 15px">
            <div id="view_map_canvas" style="width:330px; height:224px;"></div>        
        </td>
        <td style="width: 40%; padding-left: 15px">
            <?php $this->addElement('view_flow', $flow); ?>
            <br/>
            <?php $this->addElement('view_bandwidth'); ?>
        </td>
        <td style="width: 15%">      
        </td>
    </tr>
</table>

<br/><br/>

<div align="center">

    <h2><?php echo _('Timer'); ?></h2>
    
    <?php $this->addElement('view_timer', $timer); ?>

</div>

<div class="control_tab">    
    <input type="submit" id="bf"  class="ok" value="<?php echo _('Finished'); ?>"/>
    <input type="button" id="bp3" class="back" value="<?php echo _('Previous'); ?>" onClick="prevTab(this);"/>
    <input type="button" id="bc3" style="float: right" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"/>
</div>
