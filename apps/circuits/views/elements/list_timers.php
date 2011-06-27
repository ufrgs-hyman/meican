<?php

$timers = (isset($argsToElement->timers)) ? $argsToElement->timers : $argsToElement;
$selectedTimer = (isset($argsToElement->sel_timer)) ? $argsToElement->sel_timer : NULL;

?>

<table class="list">
    
    <thead>
    <tr>
        <th></th>
        <th><?php echo _("Name"); ?></th>
        <th><?php echo _("Initial Date"); ?></th>
        <th><?php echo _("Final Date"); ?></th>
        <th><?php echo _("Duration"); ?></th>
        <th><?php echo _("Recurrence"); ?></th>
    </tr>
    </thead>
    
    <tbody>
    <?php foreach ($timers as $t): ?>
    <tr>
        <td>
            <?php if ($t->deletable): ?>
                <input type="checkbox" name="del_checkbox[]" value="<?php echo $t->id; ?>">
            <?php endif; ?>

            <?php if ($t->selectable): ?>
                <?php if ($selectedTimer == $t->id): ?>
                    <input type="radio" checked="yes" name="sel_timer" value="<?php echo $t->id; ?>" onchange="changeTimer(this);">
                <?php else: ?>
                    <input type="radio" name="sel_timer" value="<?php echo $t->id; ?>" onchange="changeTimer(this);">
                <?php endif; ?>
            <?php endif; ?>
        </td>

        <td>
            <?php if ($t->editable): ?>
                <a href="<?php echo $this->buildLink(array('controller' => 'timers', 'action' => 'edit', 'param' => "tmr_id:$t->id")); ?>">
             <?php endif; ?>
             <?php echo $t->name; ?>
             <?php if ($t->editable): ?>
                </a>
             <?php endif; ?>
        </td>
        <td>
            <?php echo $t->start; ?>
        </td>
        <td>
            <?php echo $t->finish; ?>
        </td>
        <td>
            <?php echo $t->duration; ?>
        </td>
        <td>
            <?php echo $t->summary; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>

    <tfoot>
    <tr>
        <td colspan="6">
            <input class="add" type="button" value="<?php echo _("Add"); ?>" onclick="redir('<?php echo $this->buildLink(array('controller' => 'timers', 'action' => 'add_form')); ?>');">
        </td>
    </tr>
    </tfoot>

</table>