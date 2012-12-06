<?php
      
?>
    
<br/>
<input disabled type='checkbox' id='chk_maySpecifyPath' onchange='maySpecifyPath();'/> <label id="advConfLabel" disabled> <?php echo _("Advanced configurations (Select at least one waypoint to enable)"); ?> </label>
<div id='waypointsConfiguration' class='tab_content' hidden>
    <label class="label-description"> <?php echo _("Waypoints order"); ?>:</label>
    <ul id='waypoints_order'>
    </ul>
    <div id="dialog-modal" title="Setup device">
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
            <dt>
                <label class="label-description"><?php echo _("URN"); ?></label>
            </dt>
            <dd>
                <label id="partialURN"></label>
            </dd>
        </dl>
    </div>
</div>  
    

