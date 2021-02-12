<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Html;
use yii\helpers\Url;

?>
	<div style="text-align: right;">
	Last update on 10/02/2021.
	</div>
	
<div id="container_info">

    <div class="left_info">
        <div class="info_div_img" id="top">
            <?= Html::img("@web/images/meican_new.png", ['class'=>'info_img_logo', 'alt' => 'MEICAN', ]); ?>
            <h2 class="info_title">Management Environment of Inter-domain Circuits for Advanced Networks</h2>
         </div> 
	</div>
</div>
<!--TABLE OF CONTENTS-->
<div id="toc_container" style="margin-top: 30px;" style="margin-bottom: 100px;">
		<p class="toc_title"><b><?= Yii::t("home", 'Table of Contents'); ?></b></p>
		<a href="#Introduction">&ensp;1&ensp;<?= Yii::t("home", 'Introduction'); ?></a>
		<li><a href="#User tools">&ensp;2&ensp;<?= Yii::t("home", 'User Tools'); ?></a></li>
		<ul class="toc_list">
			<li><a href="#New Reservation">2.1&ensp;<?= Yii::t("home", 'New Reservation'); ?></a></li>
			<li><a href="#Reservation Status">2.2&ensp;<?= Yii::t("home", 'Reservation Status'); ?></a></li>
		</ul>
		<li><a href="#Administrator tools">&ensp;3&ensp;<?= Yii::t("home", 'Administrator Tools'); ?></a></li>
		<ul class="toc_list">
			<li><a href="#Workflows">3.1&ensp;<?= Yii::t("home", 'Workflows'); ?></a></li>
			<li><a href="#Authorization">3.2&ensp;<?= Yii::t("home", 'Authorization'); ?></a></li>
			<li><a href="#Topology Viewer">3.3&ensp;<?= Yii::t("home", 'Topology Viewer'); ?></a></li>
			<li><a href="#Topology Discovery">3.4&ensp;<?= Yii::t("home", 'Topology Discovery'); ?></a></li>
			<li><a href="#Automated Tests">3.5&ensp;<?= Yii::t("home", 'Automated Tests'); ?></a></li>
		</ul>
</div>




<div id="prints_info">
	<div class="content_info"><br>
	<!--INTRODUCTION-->
	<div class="text_info" id="Introduction"><h1><?= Yii::t("home", 'Introduction'); ?></h1></div>
	    <div class="left_info">
			<div class="info_div_text">
				<div style="padding-right: 20%;">
					<p>Management Environment of Inter-domain Circuits for Advanced Networks (MEICAN) <?= Yii::t("home", 'is a web application that enables users to request VCs between well-defined end-points that, depending on operation policies and human authorization located in the intermediate domains that connect the source and destination end-points.'); ?></p>
					<p><?= Yii::t("home", 'Our solution uses Business Process Management (BPM) concepts to manage the process of establishing virtual circuits, from VC requested by end users to configurations of network devices.'); ?></p>
					<p><?= Yii::t("home", 'The main goal of the proposed solution is to provide dynamic authorization strategies composed of policies and human support.'); ?></p>
					</div>
				</div>
			</div>
		</div>
		<br>
	<div class="content_info" id="User tools"><h1><?=Yii::t("home", "User Tools");?></h1></div>

	<div class="content_info">
	<!--NEW RESERVATION-->
	    <div class="text_info" id="New Reservation"><h2><?=Yii::t("home", "New Reservation");?></h2></div>
	    <div class="left_info">
	  	  	<div class="info_div_text">
				<p><?= Yii::t("home", 'MEICAN allows network end-users to request, in a user-friendly way, dedicated virtual circuits in Dynamic Circuits Networks (DCN). With MEICAN, you can create a circuit reservation using a map that contains networks and devices.'); ?></p>
	            <p><?= Yii::t("home", 'In addition to the points of origin and destination of the circuit, you can specify data such as the required bandwidth, waypoints and provisioning schedule.'); ?></p>
				<p><?= Yii::t("home", 'To make this process easier, MEICAN has a guide found on the right side of the screen in the'); ?>
				<a href="<?= Url::base(); ?>/circuits/reservation/create">  <span><?=Yii::t("home", 'New Reservation page.');?></span></a></p>
				<p><?= Yii::t("home", 'This guide divides the creation of the reserve into four steps:'); ?></p>
				<ul>
					<p><img src="https://maxcdn.icons8.com/Android_L/PNG/24/Maps/route-24.png" width="19"><b><?= Yii::t("home", '&ensp;Step 1: Path'); ?></b>
					<br><?= Yii::t("home", 'In this step, you need to choose the source, destination and intermediate waypoints of the circuit. You can choose these points by clicking on the domain markers on the map, or by clicking "'); ?> 
					<i class="fa fa-plus"></i>
					<?= Yii::t("home", '" in the tab on the right. After that, you can choose to complete the circuit data by default (choosing Domain, Network, Location, Port and VLAN), or in advanced (using URN and VLAN).'); ?></p>
					<p><i class="fa fa-sliders"></i><b><?= Yii::t("home", '&emsp;Step 2: Requirements'); ?></b>
				  	<br><?= Yii::t("home", 'In this step, you need to specify the bandwidth to be reserved for your circuit.'); ?></p>
				  	<p><i class="fa fa-calendar"></i><b><?= Yii::t("home", '&emsp;Step 3: Schedule'); ?></b>
				  	<br><?= Yii::t("home", 'In this step, you need to choose the start and end times for your provisioning. To do this, you must click on the desired start day for your circuit on the calendar, and fill in the duration needed.'); ?></p>
					<p><i class="fa fa-check danger"></i><b><?= Yii::t("home", '&emsp;Step 4: Confirmation'); ?></b>
					<br><?= Yii::t("home", 'Finally, you must name your reservation. Optionally, you can identify yourself by filling in the User and Access Token fields. After this, simply click on the Submit button to request your reservation.'); ?></p>
				  	
				</ul>
            </div>
    	</div>
	    <div class="right_info">
			<br>   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/reservation.png", ['class'=>'img-print']); ?>   
	    </div>
	</div>
	
	<div class="content_info"><br>
	<!--RESERVATION STATUS-->
	    <div class="text_info"  id="Reservation Status"><h2><?=Yii::t("home", "Reservation Status");?></h2></div>
	    <div class="right_info">
	  	  	<div class="info_div_text">
	  	  		<p><?= Yii::t("home", 'You can see the status of reservations to track the progress. When you created a reservation, you are automatically redirected to the status page of the created reservation.'); ?></p>
				<p><?= Yii::t("home", 'You can also find your reservation on the'); ?>
				<a href="<?= Url::base(); ?>/circuits/reservation/status">  <span><?=Yii::t("home", "Status page.");?></span></a></p>
				<p><?= Yii::t("home", 'On the status page, there are four boxes to inform the circuit provisioning status:'); ?></p>
				<ul>
					<li><?= Yii::t("home", '<b>STATUS:</b> Indicates whether the requested circuit is currently active or inactive.'); ?></li>
					<li><?= Yii::t("home", '<b>RESERVATION:</b> Displays information about the provisioning request steps. Below are the most common messages in this field and their meanings:'); ?></li>
					<ul>
						<li><?= Yii::t("home", 'Checking resources: it is displayed during the process of verifying the resources required by the user to the covered domains.'); ?></li>
						<li><?= Yii::t("home", 'Resources unavailable: it is displayed when any of the domains involved in the circuit was unable to reserve the requested resources.'); ?></li>
						<li><?= Yii::t("home", 'Provisioned: it is displayed when your reservation has been successfully received and provisioned.'); ?></li>
					</ul>
				  	<li><?= Yii::t("home", '<b>AUTHORIZATION:</b> Indicates whether the requested reservation has been approved in accordance with the previous rules established by the domain (see Workflows). If your reservation requires a manual request, the message "WAITING FOR AUTHORIZATION" will be displayed in the field.'); ?></li>
				  	<li><?= Yii::t("home", '<b>UPDATED AT:</b> Informs the time of the last reservation status update.'); ?></li>
				</ul>
				<br>
            </div>
    	</div>
		<div class="left_info">   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/reservation_status.png", ['class'=>'img-print']); ?>   
	    </div>
	</div>

	<br>
	<div class="text_info"  id="Administrator tools"><h1><?=Yii::t("home", "Administrator Tools");?></h1></div>

	<div class="content_info">
	<!--WORKFLOWS-->
	    <div class="text_info"  id="Workflows"><h2><?=Yii::t("home", "Workflows");?></h2></div> 
	    <div class="right_info">
	        <div class="info_div_text">
                <p><?= Yii::t("home", 'Authorization workflows can be used to automate the decision-making process along with multiple domains where end-users circuits pass through.'); ?></p>
	            <p><?= Yii::t("home", 'Editors users can design workflows to filter by bandwidth, domains covered, devices, duration, requester user and groups. Furthermore, a user or group can be requested to authorize manually.'); ?></p>
	        	<p><?= Yii::t("home", 'Step-by-step to manage workflows:'); ?></p>
	        	<ul>
					<li><?= Yii::t("home", 'Click Workflows - Status.'); ?></li>
				  	<li><?= Yii::t("home", 'You can Remove, Edit, Copy, Enable and Disable a workflow by clicking on the respective icons (remember that only one workflow can be active per domain at a time).'); ?></li>
				</ul>
	        	<p><?= Yii::t("home", 'Step-by-step to create a workflow:'); ?></p>
	        	<ul>
					<li><?= Yii::t("home", 'Click Workflows - New.'); ?></li>
					<li><?= Yii::t("home", 'If you are allowed for create workflows in more than one domain, select the one you want to create the workflow in.'); ?></li>
				  	<li><?= Yii::t("home", 'Type a name for your workflow.'); ?></li>
				  	<li><?= Yii::t("home", 'Drag units, drop and link the elements available on the right side of the screen.'); ?></li>
				  	<li><?= Yii::t("home", 'Click Save. If your workflow has any problem, you will receive a warning message.'); ?></li>
				</ul>
				<br>
	        </div>
			<div class="left_info">
	        <div class="info_div_img">
	        	<?= Html::img("@web/images/help/".Yii::$app->language."/workflow.png", ['class'=>'img-print']); ?>   
	        </div>
	    </div> 
	    </div>
	</div>

	<div class="content_info"><br>
	<!--AUTHORIZATION-->
	    <div class="text_info"  id="Authorization"><h2><?=Yii::t("home", "Authorization");?></h2></div>
	    <div class="left_info">
	        <div class="info_div_text">
	        	<p><?= Yii::t("home", 'In some cases, as predefined by Workflows, the reservations need to be authorized by administrators, and MEICAN enables them to accomplish these authorizations through the'); ?>
				<a href="<?= Url::base(); ?>/circuits/authorization">  <span><?=Yii::t("home", "Authorization page.");?></span></a></p>
                <p><?= Yii::t("home", 'Administrators can accept or reject users requests and leave an observation using an intelligent mechanism.'); ?></p>
	        	<p><?= Yii::t("home", 'Step-by-step to manage pending authorizations:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click Reservations - Authorization. Alternatively you can click the notifications button at the top of the screen, then click in See Authorizations.'); ?></li>
					<li><?= Yii::t("home", 'Select a reservation by clicking Answer'); ?></li>
				  	<li><?= Yii::t("home", 'Either accept or reject a reservation request by clicking the thumbs up/down icon and providing an appropriate message to the requester user, if you want. Alternatively you can Accept/Reject All requests at once.'); ?></li>
				  	<li><?= Yii::t("home", 'You should notice the status change to AUTHORIZED or DENIED in the right part of the list.'); ?></li>
				</ul>
				<br>
	        </div>
	    </div>
	    <div class="right_info">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/authorization.png", ['class'=>'img-print']); ?>   
		</div>
	</div>

	<div class="content_info"><br>
	<!--TOPOLOGY VIEWER-->
	    <div class="text_info"  id="Topology Viewer"><h2><?=Yii::t("home", "Topology Viewer");?></h2></div>
	    <div class="right_info">
	  	  	<div class="info_div_text">
				<p><?= Yii::t("home", 'The'); ?>
				<a href="<?= Url::base(); ?>/topology/viewer">  <span><?=Yii::t("home", "Topology Viewer");?></span></a>
	  	  		<?= Yii::t("home", 'is a dynamic way to view the DCN network topology known by the application.'); ?></p>
				<p><?= Yii::t("home", 'It shows devices or networks geographically located on a map with their respective links (blue lines). By clicking on a link it is possible to know the elements connected to it. We can, for example, find out what domains a hypothetical circuit must pass through to be provisioned.'); ?></p>
				<p><?= Yii::t("home", 'In addition, by clicking'); ?>
				<i class="fa fa-gear"></i>
				<?= Yii::t("home", 'on the right side of the screen, you can choose to change the view to "Graph" mode, in which we have a different way to visualize which domains are interconnected.'); ?></p>
				<br>
            </div>
    	</div>
		<div class="left_info">   
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/viewer.png", ['class'=>'img-print']); ?>   
	    </div>
	</div>

	<div class="content_info"><br>
	<!--TOPOLOGY DISCOVERY-->
	    <div class="text_info"  id="Topology Discovery"><h2><?=Yii::t("home", "Topology Discovery");?></h2></div>
	    <div class="left_info">
	        <div class="info_div_text">
				<p><?=Yii::t("home", "In the");?>
				<a href="<?= Url::base(); ?>/topology/discovery">  <span><?=Yii::t("home", "Topology Discovery page");?></span></a><?= Yii::t("home", ', it is possible, from the communication with the specified topology providers, to update the application topology. This ensures that future reservations will not fail by inconsistencies in the identifier of a network element, for example.'); ?></p>
				<p><?= Yii::t("home", 'Step-by-step to create a Discovery Rule:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click Add instance.'); ?></li>
					<li><?= Yii::t("home", 'Set a name.'); ?></li>
				  	<li><?= Yii::t("home", 'Select the type of the topology provider.'); ?></li>
				  	<li><?= Yii::t("home", 'Enable or disable the Auto apply changes.'); ?></li>
				  	<li><?= Yii::t("home", 'Enable or disable the Recurrence sync.'); ?></li>
				  	<li><?= Yii::t("home", 'Set the URL of the topology provider.'); ?></li>
				  	<li><?= Yii::t("home", 'Click Save.'); ?></li>
				</ul>
				<p><?= Yii::t("home", 'To execute a Discovery Rule:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Click on Start Discovery in the Rules list.'); ?></li>
					<li><?= Yii::t("home", 'Wait the processing.'); ?></li>
				  	<li><?= Yii::t("home", 'Verify the changes in the "Last tasks" list or on the Topology Viewer page.'); ?></li>
				</ul>
				<br>
            </div>       
	    </div>    
	    <div class="right_info">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/synchronizer.png", ['class'=>'img-print']); ?>   
	    </div>
	</div>

	<div class="content_info"><br>
	<!--AUTOMATED TESTS-->
	    <div class="text_info"  id="Automated Tests"><h2><?=Yii::t("home", "Automated Tests");?></h2></div>
	    <div class="left_info">
	        <div class="info_div_text">
                <p><?= Yii::t("home", 'This functionality perform tests on two endpoints to find errors, make logs, and report them to the administrators.'); ?></p>
                <p><?= Yii::t("home", 'To verify the status of a network, MEICAN allows administrators to program automated tests in the network environment.'); ?></p>
                <p><?= Yii::t("home", 'Step-by-step to create an Automated Test:'); ?></p>
				<ul>
					<li><?= Yii::t("home", 'Open Tests - Create.'); ?></li>
					<li><?= Yii::t("home", 'Select a port and a VLAN as source.'); ?></li>
					<li><?= Yii::t("home", 'Select a port and a VLAN as destination.'); ?></li>
				  	<li><?= Yii::t("home", 'Select the recurrence of the test.'); ?></li>
				  	<li><?= Yii::t("home", 'Click Save and wait the schedule processing (this process can take some minutes).'); ?></li>
				</ul>
				<br>
	        </div>        
	    </div>    
	    <div class="right_info"  style="margin-bottom: 100px;">
	    	<?= Html::img("@web/images/help/".Yii::$app->language."/automated_test.png", ['class'=>'img-print']); ?>   
	    </div>
	</div>

</div>

