
<?php $args = $this->passedArgs; ?>

<h1><?php echo _('Add new group')?></h1>

<form onSubmit="selectAll('used');" method="POST" action="<?php echo $this->buildLink(array('action' => 'add')); ?>">
    <table class="withoutBorder" style="min-width: 0">
        <tr>
            <td>
    <table class="withoutBorder">
        <tr>
            <th class="right">
                <?php echo _("Group name"); ?>:
            </th>
            <td class="left">
                <input type="text" size="50" name="new_group">
            </td>
        </tr>
    </table>

    <?php $this->addElement('associative_table', $args->users); ?>

    <table class="withoutBorder">
        <tr>
            <th class="right">
                <?php echo _("Select the parent groups"); ?>:
            </th>

             <td class="left">
                    <?php foreach($args->groups as $g): ?>
                        <input type="checkbox" value="<?php echo $g->grp_id; ?>" name="parents[]" /> <?php echo $g->grp_descr; ?><br>
                    <?php endforeach; ?>
            </td>
        </tr>
    </table>

    <div class="controls">
        <input class="save" type="submit" value="<?php echo _("Save"); ?>">
        <input class="cancel" type="button" value="<?php echo _("Cancel"); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>')">
    </div>
            </td>
        </tr>
    </table>
</form>