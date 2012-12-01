<?php
$workflows = $this->passedArgs;
?>

<h1><?php echo _("Workflows"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">
    <?php echo $this->element('controls', array('app' => 'init')); ?>
    <table class="list">

        <thead>
            <tr>
                <th></th>
                <th></th>
                <th><?php echo _("Name"); ?></th>
                <th><?php echo _("Owner domain"); ?></th>
                <th><?php echo _("Status"); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($workflows as $w): ?>
                <?php if ($w->status): ?>
                <tr style="background: #99ec99">
                <?php else: ?>
                <tr>
                <?php endif; ?>
                    <td>
                        <input type="checkbox" name="del_checkbox[]" value="<?php echo $w->id; ?>"/>
                    </td>
                    <td>
                        <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "id:$w->id")); ?>">
                            <img class="edit" src="<?php echo $this->url(''); ?>webroot/img/edit_1.png"/>
                        </a>
                    </td>
                    <td>
                        <?php echo $w->name; ?>
                    </td>
                    <td>
                        <?php echo $w->domain; ?>
                    </td>
                    <td>
                        <?php echo $w->status_descr; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</form>
