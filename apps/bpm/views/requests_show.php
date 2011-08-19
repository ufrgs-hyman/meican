<?php $args = $this->passedArgs;
$pending = $args->pending;
$finished = $args->finished; ?>


<h3><?php echo _("Pending incoming requests"); ?></h3>
<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">
<table class="list">
    <tr>
        <th></th>
        <th><?php echo _('Domain source') ?></th>
        <th><?php echo _('Domain destination') ?></th>
        <th><?php echo _('Requester') ?></th>
        <th><?php echo _('Resource type') ?></th>
        <th><?php echo _('Resource description') ?></th>
        <th></th>
    </tr>

    <?php foreach ($pending as $p): ?>
    <tr>
        <?php //if ($f->deletable): ?>
        <td><input type="checkbox" name="del_checkbox[]" value="<?php echo $p->loc_id; ?>" /></td>
         <?php //endif; ?>
        
        <td><?php echo $p->dom_src; ?></td>
        <td><?php echo $p->dom_dst; ?></td>
        <td><?php echo $p->usr_src; ?></td>
        <td><?php echo $p->resc_type; ?></td>
        <td><?php echo $p->resc_descr; ?></td>
        <td class="edit"><input class="answer" type="button" value="<?php echo _('Answer'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'replyRequest', 'param' => array('loc_id' => $p->loc_id))); ?> ')"></td>
        
    </tr>
    <?php endforeach; ?>
</table>

<h3><?php echo _("Finished incoming requests"); ?></h3>

    <table class="list">
    <tr>
        <th></th>
        <th><?php echo _('Domain source') ?></th>
        <th><?php echo _('Domain destination') ?></th>
        <th><?php echo _('Requester') ?></th>
        <th><?php echo _('Resource type') ?></th>
        <th><?php echo _('Resource description') ?></th>
        <th><?php echo _('Response') ?></th>
        <th><?php echo _('Message') ?></th>
    </tr>
    <?php foreach ($finished as $f): ?>
    <tr>
        <td>
                    <?php //if ($f->deletable): ?>
                    <input type="checkbox" name="del_checkbox[]" value="<?php echo $f->loc_id; ?>" />
                    <?php //endif; ?>
        </td>
        <td><?php echo $f->dom_src; ?></td>
        <td><?php echo $f->dom_dst; ?></td>
        <td><?php echo $f->usr_src; ?></td>
        <td><?php echo $f->resc_type; ?></td>
        <td><?php echo $f->resc_descr; ?></td>
        <td><?php echo $f->response; ?></td>
        <td><?php echo $f->message; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="controls">
<input class="delete" type="submit" value="<?php echo _('Delete'); ?>" onClick="return confirm('<?php echo _('The selected requests will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
</div>
</form>





