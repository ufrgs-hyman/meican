<?php 

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
                <p><b>Management Environment of Inter-domain Circuits for Advanced Networks</b> <?= Yii::t("init", 'is a Web application that enables users to request VCs between well-defined end-points that, depending on operation policies and human authorisation located in the intermediate domains that connect source and destination end-points.'); ?></p>
                <p><?= Yii::t("init", 'Our solution uses Business Process Management (BPM) concepts for managing the VCs establishment process, since VC requested by end-user to network devices configurations.'); ?></p>
                <p><?= Yii::t("init", 'The main contribution of the proposed solution is to provide dynamic authorization strategies composed for policies and human support.'); ?></p>
            </div>
        </div>
    </div> 
</div>
<div id="prints_info">
	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("init", "Reservation");?></h1></div>
	    <div class="left_info">
	  	  	<div class="info_div_text">
				<p><?= Yii::t("init", 'MEICAN allows network end-users to request, in a more user-friendly way, dedicated virtual circuits in Dynamic Circuits Networks (DCN). With MEICAN, you can create a circuit reservation using a map that contains networks and devices.'); ?></p>
	            <p><?= Yii::t("init", 'Moreover, you can specify the device, port, VLAN tag, bandwidth, and the period during which the circuit will be activated.'); ?></p>
				<p><?= Yii::t("init", 'Step-by-step:'); ?></p>
				<ul>
					<li><?= Yii::t("init", 'Click Reservations - New'); ?></li>
				  	<li><?= Yii::t("init", 'Select start and destination endpoints (you can search at the top screen) by placing the mouse over them on the map'); ?></li>
				  	<li><?= Yii::t("init", 'Complete the required (port and VLAN) information on the right side of the screen'); ?></li>
				  	<li><?= Yii::t("init", 'From the bottom of the screen type a name for your reservation'); ?></li>
				  	<li><?= Yii::t("init", 'Select the period of time you want the circuit to be active (recurrence options can be selected)'); ?></li>
				  	<li><?= Yii::t("init", 'Check the summary'); ?></li>
				  	<li><?= Yii::t("init", 'Click Request Reservation and Confirm'); ?></li>
				</ul>
            </div>
    	</div>
	    <div class="right_info">   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/reservation.png", ['class'=>'info_img']); ?>   
	    </div>
	</div>
	
	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("init", "Reservation Status");?></h1></div>
	    <div class="left_info">   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/reservation_status.png", ['class'=>'info_img']); ?>   
	    </div>
	    <div class="right_info">
	  	  	<div class="info_div_text">
	  	  		<p><?= Yii::t("init", 'You can see the status of reservations for to track progress. When you created a reservation, you are automatically redirected to status page of the created reservation.'); ?></p>
				<p><?= Yii::t("init", 'Step-by-step:'); ?></p>
				<ul>
					<li><?= Yii::t("init", 'Click Reservations - Status'); ?></li>
					<li><?= Yii::t("init", 'For see the details in map, click in the eye icon'); ?></li>
				  	<li><?= Yii::t("init", 'Status should switch quickly to SUBMITTED'); ?></li>
				  	<li><?= Yii::t("init", 'If authorization needs to be manually evaluated you should see WAITING AUTHORIZATION in the Authorization field (see Authorization)'); ?></li>
				  	<li><?= Yii::t("init", 'When the reservation is authorized Status will switch to PROVISIONED and Authorization to AUTHORIZED'); ?></li>
				</ul>
            </div>
    	</div>
	</div>
	
	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("init", "Authorization");?></h1></div>
	    <div class="left_info">
	        <div class="info_div_text">
	        	<p><?= Yii::t("init", 'In some cases, the reservations need to be authorized by administrators and MEICAN enables them to accomplish these authorizations.'); ?></p>
                <p><?= Yii::t("init", 'Administrators can accept or reject users requests and leave an observation using a intelligent mechanism.'); ?></p>
	        	<p><?= Yii::t("init", 'Step-by-step:'); ?></p>
				<ul>
					<li><?= Yii::t("init", 'Click Reservations - Authorization. Alternatively you can use the exclamation (!) sign with the number of notifications at the top part of the screen, them click in See Authorizations'); ?></li>
					<li><?= Yii::t("init", 'Select a reservation by clicking Answer'); ?></li>
				  	<li><?= Yii::t("init", 'Either accept or reject a reservation request by clicking the thumbs up or down icon and providing an appropriate message to the requester user, if you want. Alternatively you can Accept/Reject All requests at once (useful when many requests are performed through recurrence)'); ?></li>
				  	<li><?= Yii::t("init", 'You should notice the status change to AUTHORIZED or DENIED at the list on right'); ?></li>
				</ul>
	        </div>
	    </div>
	    <div class="right_info">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/authorization.png", ['class'=>'info_img']); ?>   
		</div>
	</div>

	<div class="content_info">
	    <div class="text_info"><h1><?=Yii::t("init", "Workflows");?></h1></div> 
	    <div class="left_info">
	        <div class="info_div_img">
	        	<?= Html::img("@web/images/help/".Yii::$app->language."/workflow.png", ['class'=>'info_img']); ?>   
	        </div>
	    </div>    
	    <div class="right_info">
	        <div class="info_div_text">
                <p><?= Yii::t("init", 'Authorization workflows can be used to automate the decision-making process along the multiple domains where end-users circuits pass through.'); ?></p>
	            <p><?= Yii::t("init", 'Editors users can design workflows to filter by bandwidth, involving domains, devices, duration, requester user and groups. Also, can request that a user or group to authorize manually.'); ?></p>
	        	<p><?= Yii::t("init", 'Step-by-step to Menage:'); ?></p>
	        	<ul>
					<li><?= Yii::t("init", 'Click Workflows - Status'); ?></li>
				  	<li><?= Yii::t("init", 'You can Remove, Edit, Copy, Enable and Disable a workflow by click on respective icons. (just remember that only one workflow can be active per domain at a time)'); ?></li>
				</ul>
	        	<p><?= Yii::t("init", 'Step-by-step to Create:'); ?></p>
	        	<ul>
					<li><?= Yii::t("init", 'Click Workflows - New'); ?></li>
					<li><?= Yii::t("init", 'If you are allowed for create workflows in more of one domain, you need select one of this domains'); ?></li>
				  	<li><?= Yii::t("init", 'Type a name for your workflow'); ?></li>
				  	<li><?= Yii::t("init", 'Drag units, drop and link them'); ?></li>
				  	<li><?= Yii::t("init", 'Click Save. If your workflow have any problem, you will receive a warning message'); ?></li>
				</ul>
	        </div>
	    </div>
	</div>
	
	<div class="content_info">
	    <div class="text_info"><h1>Automated Tests</h1></div>
	    <div class="left_info">
	        <div class="info_div_text">
	                <p>To verify the state of the network, MEICAN allows administrators to program automated tests in the network environment.</p>
	                <p>This functionality perform tests on two endpoints to find errors, make logs, and report them to the administrators.</p>
	        </div>        
	    </div>    
	    <div class="right_info"  style="margin-bottom: 100px;">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/automated_test.png", ['class'=>'info_img']); ?>   
	    </div>
	</div>
</div>