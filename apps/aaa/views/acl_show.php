<?php $rights = $this->passedArgs; ?>

<h1><?php echo _("Access control"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table id="acl_table" class="list" style="width: 100%">

        <thead>
            <tr>
                <th class="large" rowspan="2" colspan="3"/>
                <th class="large" colspan="2"><?php echo _("Access request object"); ?></th>
                <th class="large" colspan="2"><?php echo _("Access control object"); ?></th>
                <th class="large" rowspan="2"><?php echo _("Object type"); ?></th>
                <th class="large" colspan="4"><?php echo _("Operations"); ?></th>
            </tr>
                
            <tr>
                <th class="large"><?php echo _("Model"); ?></th>
                <th class="large"><?php echo _("Object"); ?></th>
                <th class="large"><?php echo _("Model"); ?></th>
                <th class="large"><?php echo _("Object"); ?></th>
                <th class="large"><?php echo _("Create"); ?></th>
                <th class="large"><?php echo _("Read"); ?></th>
                <th class="large"><?php echo _("Update"); ?></th>
                <th class="large"><?php echo _("Delete"); ?></th>
            </tr>
        </thead>
        
        <tbody>
        <?php foreach ($rights as $r): ?>
        <tr id="line<?php echo $r->id; ?>">

            <?php if ($r->editable): ?>
                <td>
                    <input type="checkbox" name="del_checkbox[]" value="<?php echo $r->id; ?>">
                </td>
                <td class="edit">
                    <img class="edit" src="<?php echo $this->url(''); ?>webroot/img/edit_1.png" onclick="editACL('<?php echo $r->id; ?>');">
                </td>
                <td class="edit">
                    <img class="delete" src="<?php echo $this->url(''); ?>webroot/img/remove.png" onclick="deleteACL('<?php echo $r->id; ?>');">
                </td>
            <?php else: ?>
                <td colspan="3"/>
            <?php endif; ?>

            <td id="aro_model_box<?php echo $r->id; ?>" itemid="<?php echo $r->aro_model; ?>">
                <?php echo $r->aro_model; ?>
            </td>
            <td id="aro_obj_box<?php echo $r->id; ?>" itemid="<?php echo $r->aro_obj_id; ?>">
                <?php echo $r->aro_obj; ?>
            </td>

            <td id="aco_model_box<?php echo $r->id; ?>" itemid="<?php echo $r->aco_model; ?>">
                <?php echo $r->aco_model; ?>
            </td>
            <td id="aco_obj_box<?php echo $r->id; ?>" itemid="<?php echo $r->aco_obj_id; ?>">
                <?php echo $r->aco_obj; ?>
            </td>
            
            <td id="model_box<?php echo $r->id; ?>" itemid="<?php echo $r->model; ?>"><?php echo $r->model; ?></td>

            <td id="create_box<?php echo $r->id; ?>"><?php echo $r->create; ?></td>
            <td id="read_box<?php echo $r->id; ?>"><?php echo $r->read; ?></td>
            <td id="update_box<?php echo $r->id; ?>"><?php echo $r->update; ?></td>
            <td id="delete_box<?php echo $r->id; ?>"><?php echo $r->delete; ?></td>

        </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
    
    <div class="controls">
        <img class="loading" style="display:none" id="loading" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif" />
        <input class="add" type="button" id="new_button" value="<?php echo _("New access control"); ?>" onclick="newACL();" />
        
        <input class="save" id="save_button" style="display:none" type="button"  value="<?php echo _("Save"); ?>" onclick="saveACL();"/>
        <input class="cancel" id="cancel_button" style="display:none" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">

        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected access controls will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>

</form>