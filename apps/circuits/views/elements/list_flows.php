<?php

$flows = (isset($argsToElement->flows)) ? $argsToElement->flows : $argsToElement;
$selectedFlow = (isset($argsToElement->sel_flow)) ? $argsToElement->sel_flow : NULL;

?>

<table class="list">

    <thead>
        <tr>
            <th rowspan="2"></th>
            <th rowspan="2"><?php echo _("Name"); ?></th>
            <th colspan="5"><?php echo _("Source"); ?></th>
            <th colspan="5"><?php echo _("Destination"); ?></th>
        </tr>

        <tr>
            <th><?php echo _("Domain"); ?></th>
            <th><?php echo _("Network"); ?></th>
            <th><?php echo _("Device"); ?></th>
            <th><?php echo _("Port"); ?></th>
            <th><?php echo _("VLAN"); ?></th>

            <th><?php echo _("Domain"); ?></th>
            <th><?php echo _("Network"); ?></th>
            <th><?php echo _("Device"); ?></th>
            <th><?php echo _("Port"); ?></th>
            <th><?php echo _("VLAN"); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($flows as $f): ?>
            <tr>
                <td>
                    <?php if ($f->deletable): ?>
                        <input type="checkbox" name="del_checkbox[]" value="<?php echo $f->id; ?>">
                    <?php endif; ?>

                    <?php if ($f->selectable): ?>
                        <?php if ($selectedFlow == $f->id): ?>
                            <input type="radio" checked="yes" name="sel_flow" value="<?php echo $f->id; ?>" onchange="changeFlow(this);">
                        <?php else: ?>
                            <input type="radio" name="sel_flow" value="<?php echo $f->id; ?>" onchange="changeFlow(this);">
                        <?php endif; ?>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($f->editable): ?>
                        <a href="<?php echo $this->buildLink(array('controller' => 'flows', 'action' => 'edit', 'param' => "flw_id:$f->id")); ?>">
                    <?php endif; ?>
                    <?php echo $f->name; ?>
                    <?php if ($f->editable): ?>
                        </a>
                    <?php endif; ?>
                </td>
                
                <td>
                    <?php echo $f->source->domain; ?>
                </td>
                
                <?php if (isset($f->source->urn_string)): ?>
                <td colspan="3">
                    <?php echo $f->source->urn_string; ?>
                </td>
                <?php else: ?>
                <td>
                    <?php echo $f->source->network; ?>
                </td>
                <td>
                    <?php echo $f->source->device; ?>
                </td>
                <td>
                    <?php echo $f->source->port; ?>
                </td>
                <?php endif; ?>
                
                <td>
                <?php
                    $temp = ($f->source->vlan) ? _("Tagged") . "<br>" . $f->source->vlan : _("Untagged");
                    echo $temp;
                ?>
                </td>

                <td>
                    <?php echo $f->dest->domain; ?>
                </td>

                <?php if (isset($f->dest->urn_string)): ?>
                <td colspan="3">
                    <?php echo $f->dest->urn_string; ?>
                </td>
                <?php else: ?>
                <td>
                    <?php echo $f->dest->network; ?>
                </td>
                <td>
                    <?php echo $f->dest->device; ?>
                </td>
                <td>
                    <?php echo $f->dest->port; ?>
                </td>
                <?php endif; ?>

                <td>
                <?php
                    $temp = ($f->dest->vlan) ? _("Tagged") . "<br>" . $f->dest->vlan : _("Untagged");
                    echo $temp;
                ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="12">
                <input class="add" type="button" value="<?php echo _("Add"); ?>" onclick="redir('<?php echo $this->buildLink(array('controller' => 'flows', 'action' => 'add_options')); ?>');">
            </td>

        </tr>

    </tfoot>
  
</table>