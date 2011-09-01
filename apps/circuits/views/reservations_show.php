<?php $reservations = $this->passedArgs; ?>

<h1><?php echo _("Reservations"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <input type="button" class="refresh" value="<?php echo _("Refresh"); ?>" onClick="refreshStatus();" />
    
    <br>

    <table class="list">

        <thead>
            <tr>
                <th class="checkbox"></th>
                <th></th>
                <th><?php echo _("Name"); ?></th>
                <th><?php echo _("Status"); ?></th>
                <th><?php echo _("Flow"); ?></th>
                <th><?php echo _("Timer"); ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr id="line<?php echo $r->id; ?>">
                    <td>
                        <input type="checkbox" name="del_checkbox[]" value="<?php echo $r->id; ?>"/>
                    </td>
                    <td>
                        <a href="<?php echo $this->buildLink(array('action' => 'view', 'param' => "res_id:$r->id")); ?>">
                             <img src="layouts/img/eye.png"/>
                        </a>
                    </td>

                    <td>
                        <?php echo $r->name; ?>
                    </td>

                    <td>
                        <label id="status<?php echo $r->id; ?>"></label>
                        <img alt="<?php echo _("loading"); ?>" style="display:none" id="loading" class="load" src="includes/images/ajax-loader.gif"/>
                    </td>
                    <td>
                        <?php echo $r->flow; ?>
                    </td>
                    <td>
                        <?php echo $r->timer; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="6">
                    <!-- <input class="add" type="button" value="<?php // echo _("Add"); ?>" onclick="redir('<?php //echo $this->buildLink(array('action' => 'page1')); ?>');"/>  -->
                    <input class="add" type="button" value="<?php echo _("Add"); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'reservation_add')); ?>');"/>  
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