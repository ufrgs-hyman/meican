<?php

$networks = $this->passedArgs->networks;
$domain = $this->passedArgs->domain;

?>

<h1><?php echo _("Manualy adding URNs (Uniform Resource Name)"); ?></h1>

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
        <tr id="newline0">
            <td class="edit" colspan="3">
                <img class="edit" alt="clear" border="0" src="<?php echo $this->url(''); ?>webroot/img/clear.png" onclick="deleteURNLine('0');"/>
            </td>
                    
            <td>
                <select id="network0" onchange="changeNetworkURN('<?php echo $domain->dom_id; ?>', this);" >
                    <option value="-1"/>
                    <?php foreach ($networks as $n): ?>
                        <option value="<?php echo $n->id; ?>"><?php echo $n->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
                    
            <td>
                <select style="display:none" id="device0"/>
            </td>
                    
            <td>
                <input type="text" size="3" id="port0"/>
            </td>
            <td>
                <input type="text" size="50" id="name0" value="urn:ogf:network:domain=<?php if ($domain->topology_id) echo $domain->topology_id; else echo "<domain>" ?>:node=<node>:port=<port>:link=<link>"/>
            </td>
            <td>
                <input type="text" size="10" id="vlan0"/>
            </td>
            <td>
                <input type="text" size="10" id="max_capacity0"/>
            </td>
            <td>
                <input type="text" size="10" id="min_capacity0"/>
            </td>
            <td>
                <input type="text" size="10" id="granularity0"/>
            </td>
        </tr>
    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="11">
                <input class="add" type="button" id="new_button" value="<?php echo _("Add"); ?>" onclick="newURNLine('<?php echo $domain->dom_id; ?>');" />
            </td>
        </tr>
    </tfoot>

</table>

<div class="controls">
    <input class="save" id="save_button" type="button"  value="<?php echo _("Save"); ?>" onclick="saveURN();"/>
    <input class="cancel" id="cancel_button" type="button" value="<?php echo _("Cancel"); ?>" onClick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');"/>
</div>