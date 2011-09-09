<?php $reservations = $this->passedArgs; ?>

<h1><?php echo _("Reservations"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <input type="button" class="refresh" value="<?php echo _("Refresh"); ?>" onClick="refreshStatus();" />
    
    <br>

    <table class="list" style="min-width: 105%">

        <thead>
            <tr>
                <th rowspan="2" class="checkbox"></th>
                <th rowspan="2" class="large"></th>
                <th rowspan="2" class="large"><?php echo _("Name"); ?></th>
                <th rowspan="2" class="large"><?php echo _("Bandwidth (Mbps)"); ?></th>
                <th rowspan="2" style="border-right: 1px solid black; min-width: 60px;" class="large"><?php echo _("Status"); ?></th>                
                
                <th style="border-right: 1px solid black" colspan="3"><?php echo _("Source"); ?></th>
                <th style="border-right: 1px solid black" colspan="3"><?php echo _("Destination"); ?></th>
                <th colspan="3"><?php echo _("Timer"); ?></th>
            </tr>
            <tr>
                <th class="large"><?php echo _("Domain"); ?></th>
                <th class="large"><?php echo _("Network"); ?></th>
                <th class="large" style="border-right: 1px solid black"><?php echo _("Device"); ?></th>
                
                <th class="large"><?php echo _("Domain"); ?></th>
                <th class="large"><?php echo _("Network"); ?></th>
                <th class="large" style="border-right: 1px solid black"><?php echo _("Device"); ?></th>
                
                <th class="large"><?php echo _("Start"); ?></th>
                <th class="large"><?php echo _("Finish"); ?></th>
                <th class="large"><?php echo _("Recurrence"); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr id="line<?php echo $r->id; ?>">
                    <td>
                        <input type="checkbox" name="del_checkbox[]" value="<?php echo $r->id; ?>"/>
                    </td>
                    <td style="padding-right: 5px; min-width: 20px">
                        <a href="<?php echo $this->buildLink(array('action' => 'view', 'param' => "res_id:$r->id")); ?>">
                             <img src="layouts/img/eye.png"/>
                        </a>
                    </td>

                    <td>
                        <?php echo $r->name; ?>
                    </td>
                    
                    <td>
                        <?php echo $r->bandwidth; ?>
                    </td>

                    <td>
                        <label id="status<?php echo $r->id; ?>"></label>
                        <img alt="<?php echo _("loading"); ?>" style="display:none" id="loading" class="load" src="includes/images/ajax-loader.gif"/>
                    </td>
                    <td>
                        <?php echo $r->flow->source->domain; ?>
                    </td>
                    <td>
                        <?php echo $r->flow->source->network; ?>
                    </td>
                    <td>
                        <?php echo $r->flow->source->device; ?>
                    </td>
                    <td>
                        <?php echo $r->flow->dest->domain; ?>
                    </td>
                    <td>
                        <?php echo $r->flow->dest->network; ?>
                    </td>
                    <td>
                        <?php echo $r->flow->dest->device; ?>
                    </td>
                    <td>
                        <?php echo $r->timer->start; ?>
                    </td>
                    <td>
                        <?php echo $r->timer->finish; ?>
                    </td>
                    <td>
                        <?php if ($r->timer->summary) { echo $r->timer->summary; } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="14">
                    <input class="add" type="button" value="<?php echo _("Add"); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add')); ?>');"/>  
                </td>
            </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected reservations will be deleted.');
            echo '\n';
            echo _('Do you confirm?'); ?>')"/>
    </div>

</form>