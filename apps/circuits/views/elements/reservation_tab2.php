<!-- Endpoints -->
<br/>
<div style="width:100%;">
    <table style="width:100%">
        <tr style="width:100%">
            <td style="width:68%">
                <div id="edit_map_canvas" style="width:100%; height: 400px; min-width:300px"></div>
            </td>
            <td style="width:32%">
                <div id="advOptions" style="width: 100%">
                    <table style="width: 100%; border: 1px solid #3a5879">
                        <tr>
                            <td></td>
                            <td>
                                <label id="src"><b><?php echo _(Source);?></b></label>
                            </td>
                            <td>
                                <label id="dst"><b><?php echo _(Destination);?></b></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo _(Domain);?>
                            </td>
                            <td>
                                <label id="src_domain"></label>
                            </td>
                            <td>
                                <label id="dst_domain"></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo _(Network);?>
                            </td>
                            <td>
                                <label id="src_network"></label>
                            </td>
                            <td>
                                <label id="dst_network"></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo _(Device);?>
                            </td>
                            <td>
                                <select id="src_device" style="display:none" onchange="map_changeDevice('src');"></select>
                            </td>
                            <td>
                                <select id="dst_device" style="display:none" onchange="map_changeDevice('dst');"></select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo _(Port);?>
                            </td>
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
                                <input type="checkbox" name="vlan_options" id="showVlan_checkbox" onclick="showVlanConf()"/><?php echo _("Show Vlan configuration"); ?>
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
                                <input type="button" class="add" id="addHopsButton" value="Add new hop" onclick="moreFields();"/>                                    
                                <input type="hidden" id="path" name="path"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">                                
                                <div id="div-bandwidth" style="width:100%">
                                    <hr></hr>
                                    <?php $this->addElement('reservation_tab3'); ?>
                                    <div style="width: 100%; margin-top: 5px">
                                        <label id="amount_label" for="amount"></label>
                                        <label id="amount" style="border:0; color:#000; font-weight:bold; width:100%"></label>
                                        <input type="hidden" name="bandwidth" id="bandwidth" value=""/>
                                    </div>
                                </div>
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