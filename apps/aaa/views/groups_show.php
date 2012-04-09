<?php $groups = $this->passedArgs; ?>

<h1><?php echo _("User groups"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">
    <?php echo $this->element('controls', array('app' => 'init')); ?>
    <table class="list">

        <thead>
            <tr>
                <th></th>
                <th></th>
                <th><?php echo _("Name"); ?></th>
                <th><?php echo _("Parent groups"); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($groups as $gri): ?>
                <tr>
                    <td>
                        <?php if ($gri->editable): ?>
                            <input type="checkbox" name="del_checkbox[]" value="<?php echo $gri->id; ?>" />
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($gri->editable): ?>
                            <a href="<?php echo $this->buildLink(array('action' => 'edit', 'param' => "grp_id:$gri->id")); ?>">
                                <img class="edit" src="<?php echo $this->url(''); ?>webroot/img/edit_1.png"/>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $gri->descr; ?>
                    </td>

                    <td>
                        <?php echo $gri->parents; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</form>