<!-- Endpoints -->
<br/><br/>
<div align="center" style="width:100%;">
    <table style="width:100%">
        <tr style="width:100%">
            <td style="width:10%"></td>
            <td style="width:40%">
                <div id="edit_map_canvas"></div>
            </td>
            <td style="width:40%">
                <div id="advOptions" style="width: 100%; display:none">
                    <table style="width: 100%; border: 1px solid #3a5879">
                        <tr>
                            <th></th>
                            <th>
                                <label id="src"><?php echo _(Source);?>:</label>
                            </th>
                            <th>
                                <label id="dst"><?php echo _(Destination);?>:</label>
                            </th>
                        </tr>
                        <tr>
                            <th>
                                <?php echo _(Domain);?>
                            </th>
                            <td>
                                <label id="src_domain"></label>
                            </td>
                            <td>
                                <label id="dst_domain"></label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo _(Network);?>
                            </th>
                            <td>
                                <label id="src_network"></label>
                            </td>
                            <td>
                                <label id="dst_network"></label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo _(Device);?>
                            </th>
                            <td>
                                <select id="src_device" style="display:none" onchange="map_changeDevice('src');"></select>
                            </td>
                            <td>
                                <select id="dst_device" style="display:none" onchange="map_changeDevice('dst');"></select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo _(Port);?>
                            </th>
                            <td>
                                <select id="src_port" style="display:none" onchange="map_changePort('src');"></select>
                            </td>
                            <td>
                                <select id="dst_port" style="display:none" onchange="map_changePort('dst');"></select>
                            </td>
                        </tr>
                        <tr id="vlan_tr">
                            <td colspan="3" style="padding:0px">
                                <div id="div_vlan" style="display:none">
                                    <?php $this->addElement('vlan'); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <hr/>
                                <input type="checkbox" id="showVlan_checkbox" onclick="showVlanConf()"/><?php echo _("Show Vlan Configuration"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div id="addHops" style="display:none">
                                    Select URN:
                                    <select id="selectHops" style="width: 100%"></select><br />
                                    <input type="button" class="remove" id="removeHop" value="Remove Hop" onclick="lessFields(this);"/>                                                                                                          
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <span id="writeHops"></span> 
                                <hr/>
                                <input type="button" class="add" id="addHopsButton" value="Add new Hop" onclick="moreFields();"/>                                    
                            </td>
                        </tr>
                    </table>                    
                </div>
            </td>
            <td style="width:10%"></td>
        </tr>
    </table>
    
</div>
<br/>
<!--
<div align="center" style="clear:both">        -->
    <?php /*
        $this->addElement('source_dest', $args); */
    ?>  
<!-- </div> -->
