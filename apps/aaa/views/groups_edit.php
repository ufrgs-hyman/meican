
<?php $args = $this->passedArgs; ?>

<h1><?php echo _('Edit group')?></h1>

<form onSubmit="selectAll('used');" method="POST" action="<?php echo $this->buildLink(array('action' => 'update', 'param' => 'grp_id:'.$args->group->grp_id)); ?>">

    <table>
        <tr>
            <th>
                <?php echo _("Group name"); ?>:
            </th>
            <td>
                <input type="text" name="group" value="<?php echo $args->group->grp_descr; ?>">
            </td>
        </tr>
    </table>

    <?php $this->addElement('associative_table', $args); ?>

    <div class="controls">
        <input class="save" type="submit" value="<?php echo _('Save'); ?>">
        <input class="cancel" type="button" value="<?php echo _("Cancel"); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>')">
    </div>

</form>