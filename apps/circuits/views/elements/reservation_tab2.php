<!-- Endpoints -->
<br/>
<div style="width:100%;">
    <table class="withoutBorder" style="width:100%">
        <tr style="width:100%">
            <td style="width: 1%; vertical-align: top"></td>
            <td style="vertical-align: top; width:60%; padding-right: 5px">
                <div id="edit_map_canvas" style="width:100%; height: 400px; min-width:300px"></div>
            </td>
            <td style="vertical-align: top; width:38%; padding-left: 5px">
                <div id="advOptions" style="width: 100%">
                    <table style="width: 100%; border: 1px solid #3a5879">
                        <tr>
                            <th style="border:none"></th>
                            <th style="border:none">
                                <label id="src"><?php echo _(Source);?></label>
                            </th>
                            <th style="border:none">
                                <label id="dst"><?php echo _(Destination);?></label>
                            </th>
                        </tr>
                        <tr>
                            <td  class="pad" style="color:black; font-weight: bold">
                                <?php echo _(Domain);?>
                            </td>
                            <td class="pad">
                                <label id="src_domain"></label>
                            </td>
                            <td class="pad">
                                <label id="dst_domain"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="pad" style="color:black; font-weight: bold">
                                <?php echo _(Network);?>
                            </td>
                            <td class="pad">
                                <label id="src_network"></label>
                            </td>
                            <td class="pad">
                                <label id="dst_network"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="pad" style="color:black; font-weight: bold">
                                <?php echo _(Device);?>
                            </td>
                            <td class="pad">
                                <select id="src_device" style="display:none" onchange="map_changeDevice('src');"></select>
                            </td>
                            <td class="pad">
                                <select id="dst_device" style="display:none" onchange="map_changeDevice('dst');"></select>
                            </td>
                        </tr>
                        <tr>
                            <td class="pad" style="color:black; font-weight: bold">
                                <?php echo _(Port);?>
                            </td>
                            <td class="pad">
                                <select id="src_port" style="display:none" onchange="map_changePort('src');"></select>
                                <input type="hidden" id="src_urn" name="src_urn"/>
                            </td>
                            <td class="pad">
                                <select id="dst_port" style="display:none" onchange="map_changePort('dst');"></select>
                                <input type="hidden" id="dst_urn" name="dst_urn"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="left" colspan="3" style="color: black">
                                <hr/>
                                <input disabled="disabled" type="checkbox" name="vlan_options" id="showVlan_checkbox" onclick="showVlanConf()"/><p style="vertical-align: middle; display:inline"><?php echo _("Show VLAN configuration"); ?></p>
                            </td>
                        </tr>
                        <tr id="vlan_tr">
                            <td colspan="3" style="padding:0px">
                                <div id="div_vlan" style="display:none">
                                    <br/>
                                    <?php $this->addElement('vlan'); ?>
                                    <br/><br/>
                                </div>
                            </td>
                        </tr>
                        <tr id="hops_line" style="display:none">
                            <td class="left" colspan="3">                                
                                <hr/>                                
                                <input type="button" class="add" id="addHopsButton" value="<?php echo _('Add new hop'); ?>" onclick="moreFields();" style="clear: both"/>                                    
                                <br/><br/>
                                <input type="hidden" id="path" name="path"/>
                                <span id="writeHops"></span> 
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
                        <tr>
                            <td colspan="3">                                
                                <div id="div-bandwidth" style="width:100%">
                                    <hr/>
                                    <div align="center">
                                        <?php $this->addElement('reservation_tab3'); ?>
                                    </div>                                        
                                    <div style="width: 100%; margin-top: 5px">
                                        <label id="amount_label" for="amount"></label>
                                        <label id="amount" style="border:0; color:#000; font-weight:bold; width:100%"></label>
                                        <input type="hidden" name="bandwidth" id="bandwidth" value="200"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                            
                    </table>                    
                </div>
            </td>
            <td style="width:1%"></td>
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