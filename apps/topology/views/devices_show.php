<?php $devices = $this->passedArgs; ?>

<h1><?php echo _("Devices"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">
    <?php echo $this->element('controls', array('app' => 'init')); ?>
    <table class="list" style="width: 100%">

        <thead>
            <tr>
                <th></th>
                <th></th>
                <th class="large"><?php echo _("Name"); ?></th>
                <th class="large"><?php echo _("IP address"); ?></th>
                <th class="large"><?php echo _("Trademark"); ?></th>
                <th class="large"><?php echo _("Model"); ?></th>
                <th class="large"><?php echo _("Number of ports"); ?></th>
                <th class="large"><?php echo _("Network"); ?></th>
                <th class="large"><?php echo _("Topology node ID"); ?></th>
                <th class="large"><?php echo _("#Endpoints"); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($devices as $d): ?>
                <tr>
                    <td>
                        <?php if ($d->deletable): ?>
                            <input type="checkbox" name="del_checkbox[]" value="<?php echo $d->id; ?>"/>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($d->editable): ?>
                            <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "dev_id:$d->id")); ?>">
                                <img class="edit" src="<?php echo $this->url(''); ?>webroot/img/edit_1.png"/>
                            </a>
                        <?php endif; ?>
                    </td>             
                    <td>
                        <?php echo $d->descr; ?>
                    </td>
                    <td>
                        <?php echo $d->ip; ?>
                    </td>
                    <td>
                        <?php echo $d->trademark; ?>
                    </td>
                    <td>
                        <?php echo $d->model; ?>
                    </td>
                    <td>
                        <?php echo $d->nr_ports; ?>
                    </td>
                    <td>
                        <?php echo $d->network; ?>
                    </td>
                    <td>
                        <?php echo $d->node_id; ?>
                    </td>
                    <td>
                        <?php echo $d->nr_endpoints; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>    
</form>