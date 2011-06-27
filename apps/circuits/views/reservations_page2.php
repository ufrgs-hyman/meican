<?php

$args = $this->passedArgs;
$timers_exist = isset($args->timers) ? TRUE : FALSE;

?>

<h1><?php echo _("Reservation Creation Wizard"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'page3')); ?>">

    <h2><?php echo _("Step 3 - Defining the timer"); ?></h2>

    <br>

    <?php
        if ($timers_exist)
            $this->addElement('list_timers', $args);
        else
            $this->addElement("empty_db", $args);
    ?>

    <br/><br/>

    <div class="controls">
    
    <?php if ($args->sel_timer): ?>
        <input class="next" type="submit" style="border-style:solid; border-color:green; border-width:3px;" id="next_button" value="<?php echo _("Next"); ?>">
    <?php else: ?>
        <input class="next" type="submit" disabled="disabled" id="next_button" value="<?php echo _("Next"); ?>"/>
    <?php endif; ?>
        <input class="cancel" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "show")); ?>');" value="<?php echo _("Cancel"); ?>">
    <input class="back" type="button" onClick="redir('<?php echo $this->buildLink(array("action" => "page1")); ?>');" value="<?php echo _("Previous"); ?>">
    </div>

</form>