<table class="withoutBorder">
    <tr>
        <td class="left" style="color:black">
            <?php echo _("VLAN Type"); ?>
        </td>
        <td style="color:black">
            <div align="left">
                <input type="radio" name="sourceVLANType" id="src_vlanUntagged" value="FALSE" disabled="disabled" onchange="map_changeVlanType(this,'src');"/><?php echo _("Untagged"); ?>
                <br>
                <input type="radio" name="sourceVLANType" id="src_vlanTagged" value="TRUE" disabled="disabled" onchange="map_changeVlanType(this,'src');"/><?php echo _("Tagged"); ?>
            </div>
        </td>
        <td style="color:black">
            <div align="left">
                <input type="radio" name="destVLANType" id="dst_vlanUntagged" value="FALSE" disabled="disabled" onchange="map_changeVlanType(this,'dst');"/><?php echo _("Untagged"); ?>
                <br>
                <input type="radio" name="destVLANType" id="dst_vlanTagged" value="TRUE" disabled="disabled" onchange="map_changeVlanType(this,'dst');"/><?php echo _("Tagged"); ?>
            </div>
        </td>
    </tr>

    <tr>
        <td class="left" style="color:black">
            <?php echo _("VLAN"); ?>
        </td>
        <td style="color:black">
            <input type="text" id="src_vlanText" size="14" name="src_vlan" disabled="disabled"/>
            <div id="src_vlanTip"/>
        </td>
        <td style="color:black">
            <input type="text" id="dst_vlanText" size="14" name="dst_vlan" disabled="disabled"/>
            <div id="dst_vlanTip"/>
        </td>
    </tr>
</table>