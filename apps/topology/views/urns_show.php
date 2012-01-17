<?php

$domains = $this->passedArgs;
$hasUrn = FALSE;

?>

<h1><?php echo _("URNs (Uniform resource name)"); ?></h1>

<form method="POST" action="<?php echo $this->buildLink(array('action' => 'delete')); ?>">

    <?php foreach ($domains as $dom): ?>
    <div id="domain<?php echo $dom->id; ?>">
        
        <?php if ($dom->urns): ?>
            <h2><img id="collapseExpand<?php echo $dom->id ?>" src="<?php echo $this->url(''); ?>webroot/img/minus.gif" onclick="WPToggle('#collapsableUrns<?php echo $dom->id ?>', '#collapseExpand<?php echo $dom->id ?>')"/>
                &nbsp;
            <?php
                $text = _("Domain");
                $text .= ($dom->topo_id) ? " $dom->descr - $dom->topo_id" : " $dom->descr";
                echo $text;
            ?>
            </h2>
            <div id="collapsableUrns<?php echo $dom->id ?>">                
                <?php 
                    $hasUrn = TRUE;
                    $this->addElement('list_urns',$dom); 
                 ?>
                <br/><br/>
            </div>
                 
        <?php else: ?>
            <h2>
            <?php
                $text = _("Domain");
                $text .= ($dom->topo_id) ? " $dom->descr - $dom->topo_id" : " $dom->descr";
                echo $text;
            ?>
            </h2>
            <div style="border: 1px solid black; padding-bottom: 50px; text-indent: 10px">
                <?php     
                    $args = new stdClass();
                    $args->message = ($dom->ip) ? _("No URN in this domain, click the button below to import topology from IP address")." $dom->ip" : _("No URN in this domain, choose one of the options below");
                    $args->import_link = array("action" => "import", "param" => "dom_id:$dom->id");
                    $args->add_link = array("action" => "add_manual", "param" => "dom_id:$dom->id");
                    $this->addElement("empty_urn", $args);
                ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    
    <?php if ($hasUrn): ?>
    <div class="controls">
        <input class="save" id="save_button" style="display:none" type="button"  value="<?php echo _("Save"); ?>" onclick="saveURN();"/>
        <input class="cancel" id="cancel_button" style="display:none" type="button" value="<?php echo _("Cancel"); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>

        <input class="delete" type="submit" value="<?php echo _("Delete"); ?>" onclick="return confirm('<?php echo _('The selected URNs will be deleted.'); echo '\n'; echo _('Do you confirm?'); ?>')"/>
    </div>
    <?php endif; ?>
    
</form>
