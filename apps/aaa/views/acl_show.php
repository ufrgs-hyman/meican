<?php $rights = $this->passedArgs; ?>

<h1><?php echo _("Access Control List"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <table id="acl_table" class="list">

        <thead>
            <tr>
                <th rowspan="2" colspan="3"/>
                <th colspan="2"><?php echo _("Access Request Object"); ?></th>
                <th colspan="2"><?php echo _("Access Control Object"); ?></th>
                <th rowspan="2"><?php echo _("Object Type"); ?></th>
                <th colspan="4"><?php echo _("Operations"); ?></th>
            </tr>
                
            <tr>
                <th><?php echo _("Model"); ?></th>
                <th><?php echo _("Object"); ?></th>
                <th><?php echo _("Model"); ?></th>
                <th><?php echo _("Object"); ?></th>
                <th><?php echo _("Create"); ?></th>
                <th><?php echo _("Read"); ?></th>
                <th><?php echo _("Update"); ?></th>
                <th><?php echo _("Delete"); ?></th>
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
                <img class="edit" src="layouts/img/edit_1.png" onclick="editACL('<?php echo $r->id; ?>');">
            </td>
            <td class="edit">
                <img class="delete" src="layouts/img/remove.png" onclick="deleteACL('<?php echo $r->id; ?>');">
            </td>
            <?php else: ?>
            <td colspan="3"/>
            <?php endif; ?>

            <td id="aro_model_box<?php echo $r->id; ?>" itemid="<?php echo $r->aro_model; ?>"> <!-- onmouseover="showAroDesc(<php echo $r->id; ?>)" onmouseout="hideAroDesc(<php echo $r->id; ?>)" -->
                <?php echo $r->aro_model; ?>
            </td>
            
            <td id="aro_obj_box<?php echo $r->id; ?>" itemid="<?php echo $r->aro_obj_id; ?>">
                <?php echo $r->aro_obj; ?>
            <!-- div id="aro_hint<php echo $r->id; ?>" style="display: none; visibility: hidden; background-color: #FFFFFF; border-style: solid; border-width: 1; width: 200px; height: 50px">
                <php echo $r->aro_hint; ?>
            </div -->
            </td>

            <td id="aco_model_box<?php echo $r->id; ?>" itemid="<?php echo $r->aco_model; ?>"><?php echo $r->aco_model; ?></td>
            <td id="aco_obj_box<?php echo $r->id; ?>" itemid="<?php echo $r->aco_obj_id; ?>"><?php echo $r->aco_obj; ?></td>
            
            <td id="model_box<?php echo $r->id; ?>" itemid="<?php echo $r->model; ?>"><?php echo $r->model; ?></td>

            <td id="create_box<?php echo $r->id; ?>"><?php echo $r->create; ?></td>
            <td id="read_box<?php echo $r->id; ?>"><?php echo $r->read; ?></td>
            <td id="update_box<?php echo $r->id; ?>"><?php echo $r->update; ?></td>
            <td id="delete_box<?php echo $r->id; ?>"><?php echo $r->delete; ?></td>

        </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="12">
                <img class="loading" style="display:none" id="loading" src="includes/images/ajax-loader.gif" />
                <input class="add" type="button" id="new_button" value="<?php echo _("New access control"); ?>" onclick="newACL();" />
            </td>
        </tr>
        </tfoot>

    </table>
    
    <div class="controls">
        <input class="save" id="save_button" style="display:none" type="button"  value="<?php echo _("Save"); ?>" onclick="saveACL();"/>
        <input class="cancel" id="cancel_button" style="display:none" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">

        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected access controls will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')">
    </div>

</form>