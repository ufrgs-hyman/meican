<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (empty($before))
    $before = null;
if (empty($after))
    $after = null;
?>
<div class="controls">
    <?php echo $before; ?>
    <input class="add" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')); ?>');"/>
    <input class="delete" type="submit" value="<?php echo _('Delete'); ?>" onclick="return confirm('<?php echo _('The selected items will be deleted.').'\n'._('Do you confirm?'); ?>');"/>
    <?php echo $end; ?>
</div>
<br style="clear: both"/>