<?php

$flow = $this->passedArgs->flow;
$timer = $this->passedArgs->timer;
$name = $this->passedArgs->res_name;

?>

<h1><?php echo _("Reservation Creation Wizard"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'submit')); ?>">

    <h2><?php echo _("Step 4 - Confirmation"); ?></h2>

    <br>

    <table>
        <tr>
            <th>
                <?php echo _("Reservation name"); ?>
            </th>
            <td>
                <input type="text" name="res_name" size="50" value="<?php echo $name; ?>" onchange="changeName(this);">
            </td>
        </tr>
    </table>

    <h2><?php echo _('Flow'); ?></h2>
    <?php $this->addElement('view_flow', $flow); ?>

    <h2><?php echo _('Timer'); ?></h2>
    <?php $this->addElement('view_timer', $timer); ?>


    <div class="controls">
        
        <input class="ok" type="submit" value="<?php echo _("Finish"); ?>">
        <input class="back" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "page2")); ?>');" value="<?php echo _("Previous"); ?>">
        <input class="cancel" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');" value="<?php echo _("Cancel"); ?>">
    </div>

</form>