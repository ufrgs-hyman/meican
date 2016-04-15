<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\helpers\Html;

?>
	<div style="text-align: right;">
	Last update on 05/06/2015.
	</div>
	
<div id="container_info">

    <div class="left_info">
        <div class="info_div_img">
            <?= Html::img("@web/images/meican_new.png", ['class'=>'info_img_logo', 'alt' => 'MEICAN']); ?>
            <h2 class="info_title">Management Environment of Inter-domain Circuits for Advanced Networks</h2>
        </div> 
    </div>
    <div class="right_info">
        <div class="info_div_text">
            <div style="padding-right: 20%;"><br><br>
                <p><b>Management Environment of Inter-domain Circuits for Advanced Networks</b> <?= Yii::t("home", 'is a Web application that enables users to request VCs between well-defined end-points that, depending on operation policies and human authorisation located in the intermediate domains that connect source and destination end-points.'); ?></p>
                <p><?= Yii::t("home", 'Our solution uses Business Process Management (BPM) concepts for managing the VCs establishment process, since VC requested by end-user to network devices configurations.'); ?></p>
                <p><?= Yii::t("home", 'The main contribution of the proposed solution is to provide dynamic authorization strategies composed for policies and human support.'); ?></p>
            </div>
        </div>
    </div> 
</div>
<div id="prints_info">
	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("home", "Reservation");?></h1></div>
	    <div class="left_info">
	  	  	<div class="info_div_text">
				<p><?= Yii::t("home", 'MEICAN allows network end-users to request, in a more user-friendly way, dedicated virtual circuits in Dynamic Circuits Networks (DCN). With MEICAN, you can create a circuit reservation using a map that contains networks and devices.'); ?></p>
	            <p><?= Yii::t("home", 'Moreover, you can specify the device, port, VLAN tag, bandwidth, and the period during which the circuit will be activated.'); ?></p>
				<p><?= Yii::t("home", 'Step-by-step:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click Reservations - New'); ?></li>
				  	<li><?= Yii::t("home", 'Select start and destination endpoints (you can search at the top screen) by placing the mouse over them on the map'); ?></li>
				  	<li><?= Yii::t("home", 'Complete the required (port and VLAN) information on the right side of the screen'); ?></li>
				  	<li><?= Yii::t("home", 'From the bottom of the screen type a name for your reservation'); ?></li>
				  	<li><?= Yii::t("home", 'Select the period of time you want the circuit to be active (recurrence options can be selected)'); ?></li>
				  	<li><?= Yii::t("home", 'Check the summary'); ?></li>
				  	<li><?= Yii::t("home", 'Click Request Reservation and Confirm'); ?></li>
				</ul>
            </div>
    	</div>
	    <div class="right_info">   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/reservation.png", ['class'=>'info_img']); ?>   
	    </div>
	</div>
	
	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("home", "Reservation Status");?></h1></div>
	    <div class="left_info">   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/reservation_status.png", ['class'=>'info_img']); ?>   
	    </div>
	    <div class="right_info">
	  	  	<div class="info_div_text">
	  	  		<p><?= Yii::t("home", 'You can see the status of reservations for to track progress. When you created a reservation, you are automatically redirected to status page of the created reservation.'); ?></p>
				<p><?= Yii::t("home", 'Step-by-step:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click Reservations - Status'); ?></li>
					<li><?= Yii::t("home", 'For see the details in map, click in the eye icon'); ?></li>
				  	<li><?= Yii::t("home", 'Status should switch quickly to SUBMITTED'); ?></li>
				  	<li><?= Yii::t("home", 'If authorization needs to be manually evaluated you should see WAITING AUTHORIZATION in the Authorization field (see Authorization)'); ?></li>
				  	<li><?= Yii::t("home", 'When the reservation is authorized Status will switch to PROVISIONED and Authorization to AUTHORIZED'); ?></li>
				</ul>
            </div>
    	</div>
	</div>
	
	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("home", "Authorization");?></h1></div>
	    <div class="left_info">
	        <div class="info_div_text">
	        	<p><?= Yii::t("home", 'In some cases, the reservations need to be authorized by administrators and MEICAN enables them to accomplish these authorizations.'); ?></p>
                <p><?= Yii::t("home", 'Administrators can accept or reject users requests and leave an observation using a intelligent mechanism.'); ?></p>
	        	<p><?= Yii::t("home", 'Step-by-step:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click Reservations - Authorization. Alternatively you can use the exclamation (!) sign with the number of notifications at the top part of the screen, them click in See Authorizations'); ?></li>
					<li><?= Yii::t("home", 'Select a reservation by clicking Answer'); ?></li>
				  	<li><?= Yii::t("home", 'Either accept or reject a reservation request by clicking the thumbs up or down icon and providing an appropriate message to the requester user, if you want. Alternatively you can Accept/Reject All requests at once (useful when many requests are performed through recurrence)'); ?></li>
				  	<li><?= Yii::t("home", 'You should notice the status change to AUTHORIZED or DENIED at the list on right'); ?></li>
				</ul>
	        </div>
	    </div>
	    <div class="right_info">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/authorization.png", ['class'=>'info_img']); ?>   
		</div>
	</div>

	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("home", "Workflows");?></h1></div> 
	    <div class="left_info">
	        <div class="info_div_img">
	        	<?= Html::img("@web/images/help/".Yii::$app->language."/workflow.png", ['class'=>'info_img']); ?>   
	        </div>
	    </div>    
	    <div class="right_info">
	        <div class="info_div_text">
                <p><?= Yii::t("home", 'Authorization workflows can be used to automate the decision-making process along the multiple domains where end-users circuits pass through.'); ?></p>
	            <p><?= Yii::t("home", 'Editors users can design workflows to filter by bandwidth, involving domains, devices, duration, requester user and groups. Also, can request that a user or group to authorize manually.'); ?></p>
	        	<p><?= Yii::t("home", 'Step-by-step to Menage:'); ?></p>
	        	<ul>
					<li><?= Yii::t("home", 'Click Workflows - Status'); ?></li>
				  	<li><?= Yii::t("home", 'You can Remove, Edit, Copy, Enable and Disable a workflow by click on respective icons. (just remember that only one workflow can be active per domain at a time)'); ?></li>
				</ul>
	        	<p><?= Yii::t("home", 'Step-by-step to Create:'); ?></p>
	        	<ul>
					<li><?= Yii::t("home", 'Click Workflows - New'); ?></li>
					<li><?= Yii::t("home", 'If you are allowed for create workflows in more of one domain, you need select one of this domains'); ?></li>
				  	<li><?= Yii::t("home", 'Type a name for your workflow'); ?></li>
				  	<li><?= Yii::t("home", 'Drag units, drop and link them'); ?></li>
				  	<li><?= Yii::t("home", 'Click Save. If your workflow have any problem, you will receive a warning message'); ?></li>
				</ul>
	        </div>
	    </div>
	</div>
	
	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("home", "Automated Tests");?></h1></div>
	    <div class="left_info">
	        <div class="info_div_text">
                <p><?= Yii::t("home", 'This functionality perform tests on two endpoints to find errors, make logs, and report them to the administrators.'); ?></p>
                <p><?= Yii::t("home", 'To verify the state of the network, MEICAN allows administrators to program automated tests in the network environment.'); ?></p>
                <p><?= Yii::t("home", 'Step-by-step to create a Automated Test:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Select a port and a VLAN as source.'); ?></li>
					<li><?= Yii::t("home", 'Select a port and a VLAN as destination.'); ?></li>
				  	<li><?= Yii::t("home", 'Select the recurrence of the test.'); ?></li>
				  	<li><?= Yii::t("home", 'Click Save and wait the schedule processing (two minutes).'); ?></li>
				</ul>
	        </div>        
	    </div>    
	    <div class="right_info"  style="margin-bottom: 100px;">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/automated_test.png", ['class'=>'info_img']); ?>   
	    </div>
	</div>

	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("home", "Topology Viewer");?></h1></div>
	    <div class="left_info">   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/viewer.png", ['class'=>'info_img']); ?>   
	    </div>
	    <div class="right_info">
	  	  	<div class="info_div_text">
	  	  		<p><?= Yii::t("home", 'The Topology Viewer is a dynamic way to view the DCN network topology known by the application.'); ?></p>
				<p><?= Yii::t("home", 'It shows devices or networks geographically located on a map with their respective links (blue lines). Clicking on a link is possible to know the elements connected by it.'); ?></p>
				<p><?= Yii::t("home", 'The search field at the top of the map is extremely useful for fetching elements and find out who are connected. Moreover, we can know which domains a hypothetical circuit must pass to be provisioned.'); ?></p>
            </div>
    	</div>
	</div>

	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("home", "Topology Synchronizer");?></h1></div>
	    <div class="left_info">
	        <div class="info_div_text">
				<p><?= Yii::t("home", 'The Topology Synchronizer is the element that communicates with the pre-specified topology provider to perform the update of the application topology to ensure that future reservations will not fail by inconsistencies in a, by example, identifier of a network element.'); ?></p>
				<p><?= Yii::t("home", 'Step-by-step to create a Synchronizer instance:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click Add instance'); ?></li>
					<li><?= Yii::t("home", 'Set a name'); ?></li>
				  	<li><?= Yii::t("home", 'Select the type of the topology provider.'); ?></li>
				  	<li><?= Yii::t("home", 'Enable or disable the Auto apply changes.'); ?></li>
				  	<li><?= Yii::t("home", 'Enable or disable the Recurrence sync.'); ?></li>
				  	<li><?= Yii::t("home", 'Set the URL of the topology provider.'); ?></li>
				  	<li><?= Yii::t("home", 'Click Save'); ?></li>
				</ul>
				<p><?= Yii::t("home", 'To execute a Synchronizer instance:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click on Sync icon {icon} on Synchronizer instances list.', ['icon'=>
						Html::img("@web/images/arrow_circle_double.png")]); ?></li>
					<li><?= Yii::t("home", 'Wait the processing.'); ?></li>
				  	<li><?= Yii::t("home", 'Verify the changes on notifications or on Topology Viewer.'); ?></li>
				</ul>
            </div>       
	    </div>    
	    <div class="right_info"  style="margin-bottom: 100px;">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/synchronizer.png", ['class'=>'info_img']); ?>   
	    </div>
	</div>
</div>