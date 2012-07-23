<!-- CONFIRMATION -->
<table id="confirmation_endpoints">
    <tr>
        <td style="width: 1%; vertical-align: top"></td>
        <td style="width: 60%; padding-right: 5px; vertical-align: top">
            <div id="view_map_canvas" style="width:100%; height:400px;"></div>        
        </td>
        <td style="width: 38%; padding-left: 5px; vertical-align: top">
            <?php $this->addElement('view_flow', @$flow); ?>
            <br/>
            <?php $this->addElement('view_bandwidth'); ?>
            <br/>
            <?php $this->addElement('view_timer', @$timer); ?>
        </td>
        <td style="width: 1%; vertical-align: top"></td>
    </tr>
</table>
