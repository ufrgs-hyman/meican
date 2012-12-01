<?php
    $domains = $this->passedArgs;
?>

<div>
            <span><?= _("Select the owner domain") ?>:</span>
            <?php if (count($domains) == 1): ?>
            <select id="owner_dom_id" disabled>
            <?php else: ?>
            <select id="owner_dom_id">
            <?php endif; ?>
                <?php foreach ($domains as $d): ?>
                <option value="<?= $d->dom_id ?>"><?= $d->dom_descr ?></option>
                <?php endforeach; ?>
            </select>
        </div>
<iframe src="<?php echo $this->url(array('action' => 'show_frame')); ?>"> </iframe>