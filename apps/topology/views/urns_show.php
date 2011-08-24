<?php

$domains = $this->passedArgs;
$hasUrn = FALSE;

?>

<h1><?php echo _("URNs (Uniform Resource Name)"); ?></h1>

<?php echo _("Select the domain to view"); ?>:
<select onchange="changeURNDomain(this);">
    <option value="-1"><?php echo _("Show all"); ?></option>
    <?php foreach ($domains as $dom): ?>
        <option value="<?php echo $dom->id ?>"><?php echo $dom->descr ?></option>
    <?php endforeach; ?>
</select>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <?php foreach ($domains as $dom): ?>
    <div id="domain<?php echo $dom->id; ?>">
        <h2><?php echo _("Domain")." $dom->descr - $dom->topo_id"; ?></h2>
        
        <?php if ($dom->urns): ?>
            <?php
                $hasUrn = TRUE;
                $this->addElement('list_urns',$dom);
            ?>
        <?php else:
            $args = new stdClass();
            $args->message = _("No URN in this domain, click the button below to import topology from IP address")." $dom->ip";
            $args->link = array("action" => "import", "param" => "dom_id:$dom->id");
            
            $this->addElement("empty_db", $args);
        ?>
            <br/>
            <br/>
            <br/>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    
    <?php if ($hasUrn): ?>
    <div class="controls">
        <input class="save" id="save_button" style="display:none" type="button"  value="<?php echo _("Save"); ?>" onclick="saveURN();"/>
        <input class="cancel" id="cancel_button" style="display:none" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>

        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onClick="return confirm('<?php echo _('The selected URNs will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')"/>
    </div>
    <?php endif; ?>
    
</form>