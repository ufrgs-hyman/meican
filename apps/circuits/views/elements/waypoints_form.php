<?php
      
?>
    
<br/>
<input disabled type='checkbox' id='chk_maySpecifyPath' onchange='maySpecifyPath();'/> <label id="advConfLabel" disabled> <?php echo _("Advanced configurations (Select at least one waypoint to enable)"); ?> </label>
<div id='waypointsConfiguration' class='tab_content' hidden>
    <h3><?php echo _("Waypoints order"); ?>:</h3>
    <ul id='waypoints_order'>
    </ul>
</div>  
    