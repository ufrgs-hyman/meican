<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (empty($before))
    $before = null;
if (empty($after))
    $after = null;
if (empty($buttons))
    $buttons = array('add', 'delete');
?>
<div class="controls">
    <?php echo $before; ?>
    <?php if (in_array('add', $buttons)): ?>
    <input class="add" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add_form')); ?>');"/>
    <?php endif; ?>
    <?php if (in_array('delete', $buttons)): ?>
    <input id="DeleteButton" class="delete" type="submit" value="<?php echo _('Delete'); ?>" onclick="return confirm('<?php echo _('The selected items will be deleted.').'\n'._('Do you confirm?'); ?>');"/>
    <?php endif; ?>
    <?php echo $after; ?>
</div>
<div style="clear: both"></div>