<?php

$reservations = $this->passedArgs->reservations;
$refresh = $this->passedArgs->refresh;

?>

<h1><?php if ($refresh) echo _("Active and pending reservations"); else echo _("History reservations"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <?php
    $arrayElem = ($refresh)
        ? array('app' => 'init',
                'before' => '<input type="button" class="refresh" value="' . _("Refresh") . '" onclick="refreshStatus();" />')
        : array('app' => 'init', 'buttons' => array('delete'));
    
    echo $this->element('controls', $arrayElem);
    ?>

    <table class="list">
        <thead>
            <tr>
                <th class="checkbox"></th>
                <th class="large"></th>
                <th class="large"><?php echo _("Name"); ?></th>
                <th class="large" style="width:82px;"><?php echo _("Bandwidth (Mbps)"); ?></th>
                <th class="large" style="width:64px;"><?php echo _("Status"); ?></th>                

                <th class="large"><?php echo _("Source"); ?></th>
                <th class="large"><?php echo _("Destination"); ?></th>
                <th class="large"><?php echo _("Start"); ?></th>
                <th class="large"><?php echo _("Finish"); ?></th>
                <th class="large" style="width:230px;"><?php echo _("Recurrence"); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr id="line<?php echo $r->id; ?>">
                    <td>
                        <input type="checkbox" name="del_checkbox[]" value="<?php echo $r->id; ?>"/>
                    </td>
                    <td style="padding-right: 5px; min-width: 20px">
                        <a href="<?php echo $this->buildLink(array('action' => 'view', 'param' => "res_id:$r->id,refresh:$refresh")); ?>">
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
                        <label id="status<?php echo $r->id; ?>">
                            <?php echo $r->status; ?>
                        </label>
                        <img alt="<?php echo _("loading"); ?>" style="display:none" id="loading<?php echo $r->id; ?>" class="load<?= $r->flow->source->dom_id ?>" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif"/>
                    </td>
                    <td>
                        <?php echo $r->flow->source->domain; ?>
                        <?php echo $r->flow->source->network; ?>
                        <?php //echo $r->flow->source->device;  ?>
                    </td>
                    <td>
                        <?php echo $r->flow->dest->domain; ?>
                        <?php echo $r->flow->dest->network; ?>
                        <?php //echo $r->flow->dest->device;  ?>
                    </td>
                    <td>
                        <?php echo $r->timer->start; ?>
                    </td>
                    <td>
                        <?php echo $r->timer->finish; ?>
                    </td>
                    <td>
                        <?php
                        if ($r->timer->summary) {
                            echo $r->timer->summary;
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</form>
