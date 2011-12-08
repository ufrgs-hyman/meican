<?php $reservations = $this->passedArgs; ?>

<h1><?php echo _("Reservations"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">


    <div class="controls">
        <input type="button" class="refresh" value="<?php echo _("Refresh"); ?>" onClick="refreshStatus();" />
        <input class="add" type="button" value="<?php echo _("Add"); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'add')); ?>');"/> 
        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php
echo _('The selected reservations will be deleted.');
echo '\n';
echo _('Do you confirm?');
?>')"/>
    </div>

    <br>

    <table class="list" style="min-width: 105%">

        <thead>
            <tr>
                <th class="checkbox"></th>
                <th class="large"></th>
                <th class="large"><?php echo _("Name"); ?></th>
                <th class="large" style="width:10%;"><?php echo _("Bandwidth (Mbps)"); ?></th>
                <th class="large"><?php echo _("Status"); ?></th>                

                <th class="large"><?php echo _("Source"); ?></th>
                <th class="large"><?php echo _("Destination"); ?></th>
                <th class="large"><?php echo _("Start"); ?></th>
                <th class="large"><?php echo _("Finish"); ?></th>
                <th class="large" style="width:20%;"><?php echo _("Recurrence"); ?></th>
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
                            <img src="<?php echo $this->url() ?>webroot/img/eye.png" alt="<?php echo _('View'); ?>"/>
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
                        <img alt="<?php echo _("loading"); ?>" style="display:none" id="loading<?php echo $r->id; ?>" class="load" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif"/>
                    </td>
                    <td>
                        <?php echo $r->flow->source->domain; ?><br/>
                        <?php echo $r->flow->source->network; ?>
                        <?php //echo $r->flow->source->device; ?>
                    </td>
                    <td>
                        <?php echo $r->flow->dest->domain; ?><br/>
                        <?php echo $r->flow->dest->network; ?>
                        <?php //echo $r->flow->dest->device; ?>
                    </td>
                    <td>
                        <?php echo $r->timer->start; ?>
                    </td>
                    <td>
                        <?php echo $r->timer->finish; ?>
                    </td>
                    <td>
                <?php if ($r->timer->summary) {
                    echo $r->timer->summary;
                } ?>
                    </td>
                </tr>
<?php endforeach; ?>
    </table>

</form>
