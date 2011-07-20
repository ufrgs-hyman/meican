<?php
$domains = $this->passedArgs;
?>

<h1><?php echo _("Importing Topology URNs (Uniform Resource Name)"); ?></h1>
<h2><?php echo _("Select a domain to import from"); ?></h2>

<table class="list">

    <thead>
        <tr>
            <th><?php echo _("Name"); ?></th>
            <th><?php echo _("OSCARS IP"); ?></th>
            <th><?php echo _("Topology Domain ID"); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($domains as $d): ?>
            <tr>
                <td>
                    <a href="<?php echo $this->buildLink(array('action' => 'import', 'param' => "dom_id:$d->id")); ?>">
                        <?php echo $d->descr; ?>
                    </a>
                </td>
                <td>
                    <?php echo $d->oscars_ip; ?>
                </td>
                <td>
                    <?php echo $d->topo_domain_id; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>

<div class="controls">
    <input class="cancel" id="cancel_button" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
</div>