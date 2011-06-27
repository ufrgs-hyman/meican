<?php

$args = $this->passedArgs;
$res_wizard = $args->res_wizard;

?>

<h1><?php echo _("Timer Components"); ?></h1>

<?php if ($res_wizard): ?>
<h2><?php echo _("Reservation Creation Wizard"); ?></h2>
<?php endif; ?>

<form method="POST">

    <table>
        <tr>
            <th>
                <?php echo _("Timer name") ?>
            </th>
            <td colspan="4">
                <input id="name" type="text" size="50">
            </td>
        </tr>
    </table>

    <?php $this->addElement('timer_form', $args); ?>

    <div class="controls">
        <input type="button" class="ok" value="<?php echo _("OK"); ?>" onclick="saveTimer();">
        <?php if ($res_wizard): ?>
        <input class="cancel" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'reservations', 'action' => 'page2')); ?>')">
        <?php else: ?>
        <input type="button" class="cancel" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('controller' => 'timers', 'action' => 'show')); ?>')">
        <?php endif; ?>
    </div>
    
</form>