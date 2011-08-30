<?php $args = $this->passedArgs; ?>

<h1><?php echo _("Add device"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array("action" => "add")); ?>" onsubmit="validateDeviceForm();">

    <table style="min-width: 0">
        <tr>
            <td>
                <?php $this->addElement('device_form', $args); ?>
            </td>
        </tr>
        <tr>
            <td>
        <div class="controls">
            <input type="submit" class="save" value="<?php echo _('Save'); ?>"/>
            <input type="button" class="cancel" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
        </div>
                
            </td>
        </tr>
    </table>    
</form>