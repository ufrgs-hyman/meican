<table>
    <tr>
        <th>
            <?php echo _("VLAN Type"); ?>
        </th>
        <td align="left">
            <input type="radio" name="sourceVLANType" id="src_vlanUntagged" value="FALSE" disabled="disabled" onchange="map_changeVlanType(this,'src');"/><?php echo _("Untagged"); ?>
            <br>
            <input type="radio" name="sourceVLANType" id="src_vlanTagged" value="TRUE" disabled="disabled" onchange="map_changeVlanType(this,'src');"/><?php echo _("Tagged"); ?>
        </td>
        <td align="left">
            <input type="radio" name="destVLANType" id="dst_vlanUntagged" value="FALSE" disabled="disabled" onchange="map_changeVlanType(this,'dst');"/><?php echo _("Untagged"); ?>
            <br>
            <input type="radio" name="destVLANType" id="dst_vlanTagged" value="TRUE" disabled="disabled" onchange="map_changeVlanType(this,'dst');"/><?php echo _("Tagged"); ?>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo _("VLAN"); ?>
        </th>
        <td>
            <input type="text" id="src_vlanText" name="src_vlan" disabled="disabled" onkeyup="changeTagValue('src');"/>
            <div id="src_vlanTip"/>
        </td>
        <td>
            <input type="text" id="dst_vlanText" name="dst_vlan" disabled="disabled" onkeyup="changeTagValue('dst');"/>
            <div id="dst_vlanTip"/>
        </td>
    </tr>
</table>