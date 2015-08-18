<?php 
	use app\modules\circuits\assets\CreateReservationAsset;
	use app\assets\GoogleMapsAsset;
	use yii\widgets\ActiveForm;
    use yii\jui\AutoComplete;
	
	CreateReservationAsset::register($this);
	GoogleMapsAsset::register($this);

	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => 'create',
			'id' => 'reservation-form',
	])
?>

<div id="search-row" style="margin-left: 20px; margin-top:15px;" hidden>
    <div id="testeteste">
    	<input type="text" id="search-box" size="40">
    	<button id="search-button"><span class="ui-icon-to-button-without-background ui-icon ui-icon-search" style="margin-left: 35%;"></span></button>
	</div>
</div>

<div id="subtab-points" class="tab_subcontent">
	<?= $this->render('_selectMarkerType', array('type' => 'network')); ?>
	<br>
	<?= $this->render('_formEndpoints', array('label' => Yii::t("circuits", "Source"), 'prefix' => 'src')); ?>
	<div id="bandwidth_bar">
		<div id="bandwidth_bar_text" style="width:0px;">
			<div style="text-align:center;">
				<input type="text" value="600" name="ReservationForm[bandwidth]" id="bandwidth" class="integer-input" size="4" step="100" disabled="disabled"/>
			</div>
			<label id="bandwidth_un" for="bandwidth">Mbps</label>
		</div>
		<div id="bandwidth_bar_inside"></div>
	</div>
	<?= $this->render('_formEndpoints', array('label' => Yii::t("circuits", "Destination"), 'prefix' => 'dst')); ?>
</div>

<div id="reservation-tab">
	<div id="recurrence-date-time"><?= $this->render('_formRecurrence'); ?><br><button id="request-button"><?= Yii::t("circuits", "Request reservation"); ?></button></div>
	<div id="reservation-waypoints"><?= $this->render('_formWaypoints'); ?></div>
	<div id="reservation-request"></div>
</div>

<div id="waypoint-dialog" title="<?= Yii::t("circuits", "Configure waypoint"); ?>" hidden>
        <dl>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Domain"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="waypoint-domain" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Network"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="waypoint-network" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Device"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="waypoint-device" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "Port"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="waypoint-port" disabled></select>
            </dd>
            <dt>
                <label class="label-description"><?= Yii::t("circuits", "VLAN"); ?>:</label>
            </dt>
            <dd>
                <select style="width: 180px;" id="waypoint-vlan" disabled></select>
            </dd>
        </dl>
</div>

    <div id="edp_dialog_form" title="<?= "Search for endpoint"; ?>" hidden>
        <label for="edp_reference"><?= "Fill in with a hostname, IP address or URN"; ?></label>
        <br/>
        <input type="text" name="edp_reference" id="edp_reference" size="50" style="margin-top: 10px; margin-bottom: 7px;" placeholder="<?= 'Enter text'; ?>" title="<?= 'Hostname, IP address or URN'; ?>"/>
        <input type="hidden" id="edp_dialog"/>
        <br/>
        <label id="dialog_msg"></label>
    </div>

<div id="copy-urn-dialog" title="<?= Yii::t("circuits", "Copy the endpoint identifier");?>" hidden>
    <label for="copy-urn-field">URN:</label>
    <br/>
    <input readonly="true" type="text" id="copy-urn-field" size="50" style="margin-top: 10px;" value="urn"/>
</div>

<div id="confirm-dialog" title="<?= Yii::t("circuits", "Confirm"); ?>" hidden>
	<br>
    <label><?= Yii::t("circuits", "Do you confirm the request?"); ?></label>
    <br/><br>
    <label id="error-confirm-dialog" class="reservation-form-error"></label>
</div>

<label id="domains-list" hidden><?= json_encode($domains); ?></label>

<?php
	ActiveForm::end();
?>