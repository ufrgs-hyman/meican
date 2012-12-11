<?php
      
?>
    
<div id='waypointsConfiguration' class='tab_content'>
    <label class="label-description"> <?php echo _("Waypoints order"); ?>:</label>
    <ul id='waypoints_order'>
    </ul>
    <div id="dialog-modal" title="<?php echo _('Setup device'); ?>">
        <dl>
            <dt>
                <label class="label-description"><?php echo _("Domain") ?>:</label>
            </dt>
            <dd>
                <label id="waypointDomain"></label>
            </dd>
            <dt>
                <label class="label-description"><?php echo _("Network") ?>:</label>
            </dt>
            <dd>
                <label id="waypointNetwork"></label>
            </dd>
            <dt>
                <label class="label-description"><?php echo _("Device") ?>:</label>
            </dt>
            <dd>
                <select id="waypointDevice" onchange="completeURN();"></select>
            </dd>
        </dl>
    </div>
</div>  
    

