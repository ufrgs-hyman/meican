<?php

$urns = $this->passedArgs->urns;
$networks = $this->passedArgs->networks;
$domain = $this->passedArgs->domain;

?>

<h1><?php echo _("Importing topology URNs (Uniform Resource Name)"); ?></h1>

<h2>
    <?php
    $text = _("Domain");
    $text .= ($domain->topology_id) ? " $domain->dom_descr - $domain->topology_id" : " $domain->dom_descr";
    echo $text;
    ?>
</h2>

<table id="urn_table<?php echo $domain->dom_id; ?>" class="list">

    <thead>
        <?php $this->addElement('urn_header'); ?>
    </thead>

    <tbody>
        <?php foreach ($urns as $u): ?>
            <tr id="newline<?php echo $u->id; ?>">
                <td class="edit" colspan="3">
                    <img class="delete" src="<?php echo $this->url(''); ?>webroot/img/delete.png" onclick="deleteURNLine('<?php echo $u->id; ?>');"/>
                </td>

                <td>
                    <select id="network<?php echo $u->id; ?>" onchange="changeNetworkURN('<?php echo $domain->dom_id; ?>', this);" >
                        <option value="-1"/>
                        <?php
                        $dev_found = FALSE;
                        foreach ($networks as $n):
                            // se não encontrou o dispositivo do URN, procura por ele
                            if (!$dev_found) {
                                foreach ($n->devices as $d) {
                                    if ($d->node_id == $u->node_id) {
                                        $net_id = $n->id;
                                        $dev_id = $d->id;
                                        $devices = $n->devices;
                                        $dev_found = TRUE;
                                        break;
                                    }
                                }
                            }
                            
                            if ($dev_found && ($net_id == $n->id)): ?>
                                <option selected="true" value="<?php echo $n->id; ?>"><?php echo $n->name; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $n->id; ?>"><?php echo $n->name; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </td>

                <td>
                    <?php if ($dev_found): ?>
                        <select id="device<?php echo $u->id; ?>">
                            <option value="-1"/>
                            <?php foreach ($devices as $d): ?>
                                <?php if ($d->id == $dev_id): ?>
                                    <option selected="true" value="<?php echo $d->id; ?>"><?php echo $d->name; ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $d->id; ?>"><?php echo $d->name; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <select style="display:none" id="device<?php echo $u->id; ?>"/>
                    <?php endif; ?>
                </td>

                <td><?php echo $u->port; ?></td>
                <td><?php echo $u->name; ?></td>
                <td><?php echo $u->vlan; ?></td>
                <td><?php echo $u->max_capacity; ?></td>
                <td><?php echo $u->min_capacity; ?></td>
                <td><?php echo $u->granularity; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>

<div class="controls">
    <input class="save" id="save_button" type="button"  value="<?php echo _("Save"); ?>" onclick="saveURN();"/>
    <input class="cancel" id="cancel_button" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
</div>