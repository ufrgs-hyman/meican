<?php

$args = $this->passedArgs;
$name = $args->res_name;
$flows_exist = isset($args->flows) ? TRUE : FALSE;

?>

<h1><?php echo _("Reservation Creation Wizard"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'page2')); ?>">

    <h2><?php echo _("Step 1 - Defining the reservation name"); ?></h2>

    <br>

    <table>
        <tr>
            <th>
                <?php echo _("Reservation name"); ?>
            </th>
            <th>
                <input type="text" size="50" value="<?php echo $name; ?>" onchange="changeName(this);">
            </th>
        </tr>
    </table>

    <br/><br/>

    <h2><?php echo _("Step 2 - Defining the flow"); ?></h2>

    <?php
        if ($flows_exist)
            $this->addElement('list_flows', $args);
        else
            $this->addElement("empty_db", $args);
    ?>
    
    <div class="controls">
      <?php if ($args->sel_flow): ?>
        <input id="next_button" class="next" type="submit" value="<?php echo _("Next"); ?>" >
    <?php else: ?>
        <input id="next_button" class="next" type="submit" disabled="disabled" value="<?php echo _("Next"); ?>" >
    <?php endif; ?>
        <input class="cancel" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');" value="<?php echo _("Cancel"); ?>">
    </div>
    
</form>