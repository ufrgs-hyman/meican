<!-- Endpoints -->
<br/>
<div style="width:100%;">
    <table style="width:100%">
        <tr style="width:100%">
            <td style="width:50%">
                <div id="edit_map_canvas" style="width:100%; min-width:200px"></div>
            </td>
            <td style="width:45%">
                <div id="advOptions" style="width: 100%">
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
                                <input type="hidden" id="src_urn" name="src_urn"/>
                            </td>
                            <td>
                                <select id="dst_port" style="display:none" onchange="map_changePort('dst');"></select>
                                <input type="hidden" id="dst_urn" name="dst_urn"/>
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
                                <input type="checkbox" name="vlan_options" id="showVlan_checkbox" onclick="showVlanConf()"/><?php echo _("Show Vlan Configuration"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div id="addHops" style="display:none">
                                    Select URN:
                                    <img class="delete" id="removeHop" onclick="lessFields(this);" src="layouts/img/remove.png"/>
                                    <select id="selectHops" style="width: 100%" onchange="edit_mapPlaceDevice();"></select><br />
                                </div>
                            </td>
                        </tr>
                        <tr id="hops_line">
                            <td colspan="3">
                                <span id="writeHops"></span> 
                                <hr/>
                                <input type="button" id="clearpath" class="clear" value="<?php echo _('Clear'); ?>" onClick="edit_clearAll();"/>
                                <input type="button" class="add" id="addHopsButton" value="Add new Hop" onclick="moreFields();"/>                                    
                                <input type="hidden" id="path" name="path"/>
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
