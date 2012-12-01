<?php
    $domains = isset($this->passedArgs->domains) ? $this->passedArgs->domains : $this->passedArgs;
    $dom_id = isset($this->passedArgs->dom_id) ? $this->passedArgs->dom_id : NULL;
?>

<div>
    <span><?= _("Select the owner domain") ?>:</span>
    <?php if (count($domains) == 1): ?>
        <select id="owner_dom_id" disabled>
    <?php else: ?>
        <select id="owner_dom_id">
    <?php endif; ?>
        <?php foreach ($domains as $d): ?>
            <?php if ($dom_id && ($dom_id == $d->dom_id)): ?>
                <option selected="true" value="<?= $d->dom_id ?>"><?= $d->dom_descr ?></option>
            <?php else: ?>
                <option value="<?= $d->dom_id ?>"><?= $d->dom_descr ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
        </select>
</div>

<iframe name="workflow_editor" src="<?php echo $this->url(array('action' => 'show_frame')); ?>"> </iframe>

<div class="controls">
    <input type="submit" id="bt_save_workflow" class="save" value="<?php echo _('Save'); ?>" "/>
    <input type="button" class="cancel" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
</div>