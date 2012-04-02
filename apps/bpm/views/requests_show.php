<?php
$args = $this->passedArgs;
$pending = $args->pending;
$finished = $args->finished;
?>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <?php
    $arrayElem = array('app' => 'init', 'buttons' => array('delete'));
    echo $this->element('controls', $arrayElem);
    ?>

    <?php if ($pending): ?>
        <h3><?php echo _("Pending incoming requests"); ?></h3>

        <table class="list">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo _('Source domain') ?></th>
                    <th><?php echo _('Destination domain') ?></th>
                    <th><?php echo _('Requester') ?></th>
                    <th><?php echo _('Resource type') ?></th>
                    <th><?php echo _('Resource description') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $p): ?>
                    <tr>
                        <?php //if ($f->deletable):  ?>
                        <td>
                            <input type="checkbox" name="del_checkbox[]" value="<?php echo $p->loc_id; ?>" />
                        </td>
                        <?php //endif;  ?>

                        <td><?php echo $p->src_domain; ?></td>
                        <td><?php echo $p->dst_domain; ?></td>
                        <td><?php echo $p->src_user; ?></td>
                        <td><?php echo $p->resc_type; ?></td>
                        <td><?php echo $p->resc_descr; ?></td>
                        <td class="edit">
                            <input class="answer" type="button" value="<?php echo _('Answer'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'reply', 'param' => array('loc_id' => $p->loc_id))); ?> ')"/>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($finished): ?>
        <h3><?php echo _("Finished incoming requests"); ?></h3>

        <table class="list">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo _('Source domain') ?></th>
                    <th><?php echo _('Destination domain') ?></th>
                    <th><?php echo _('Requester') ?></th>
                    <th><?php echo _('Resource type') ?></th>
                    <th><?php echo _('Resource description') ?></th>
                    <th><?php echo _('Response') ?></th>
                    <th><?php echo _('Message') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($finished as $f): ?>
                    <tr>
                        <td>
                            <?php //if ($f->deletable): ?>
                            <input type="checkbox" name="del_checkbox[]" value="<?php echo $f->loc_id; ?>" />
                            <?php //endif;  ?>
                        </td>
                        <td><?php echo $f->src_domain; ?></td>
                        <td><?php echo $f->dst_domain; ?></td>
                        <td><?php echo $f->src_user; ?></td>
                        <td><?php echo $f->resc_type; ?></td>
                        <td><?php echo $f->resc_descr; ?></td>
                        <td><?php echo $f->response; ?></td>
                        <td><?php echo $f->message; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</form>