<?php
$requests = $argsToElement;
?>
<table border="1">

    <tr>
        
        <td><?php echo _('Domain Source') ?></td>
        <td><?php echo _('Requester') ?></td>
        <td><?php echo _('Response') ?></td>
        <td><?php echo _('Message') ?></td>
    </tr>

    <?php foreach ($requests as $req): ?>
    <?php if ($req->status): ?>
    <tr class="wait">
            <td><?php echo $req->dom_src; ?></td>
            <td><?php echo $req->usr_src; ?></td>
            <td>
                <a href="<?php echo $this->buildLink(array('action' => 'replyRequest', 'param' => array('loc_id' => $req->loc_id))); ?>"><?php echo _('Answer') ?></a>
            </td>
    </tr>
    <?php else: ?>
       <?php endif; ?>
    </tr>
    <?php endforeach; ?>

</table>
