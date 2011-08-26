<!-- TIMER -->
<br/>
<table class="withoutBorder">
    <tr>
        <td style="width:1%"></td>
        <td>
            <?php $this->addElement('timer_form', $argsToElement); ?>
        </td>
    </tr>
</table>
<br/>
<div class="control_tab">

    <input type="button" id="bn2" class="next ui-state-disabled" value="<?php echo _('Next'); ?>" onClick="nextTab(this);"/>
    <input type="button" id="bp2" class="back" value="<?php echo _('Previous'); ?>" onClick="prevTab(this);"/>
    <input type="button" id="bc2" style="float: right" class="cancel" value="<?php echo _('Cancel'); ?>" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');"/>          
</div>
