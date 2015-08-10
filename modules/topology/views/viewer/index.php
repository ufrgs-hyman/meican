<?php 
	use app\modules\topology\assets\ViewerAsset;
	use app\assets\GoogleMapsAsset;
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\helpers\Url;
	use yii\widgets\Pjax;
	
	ViewerAsset::register($this);
	GoogleMapsAsset::register($this);
?>

<div id="search-row" style="margin-left: 20px; margin-top:15px;" hidden>
    <input type="text" id="search-box" size="40">
    <button id="search-button"><span class="ui-icon-to-button-without-background ui-icon ui-icon-search" style="margin-left: 35%;"></span></button>
</div>

<div id="subtab-points" class="tab_subcontent">
	<table class="reservation-marker-type reservation-point view-point">
	    <thead>
	        <tr>
	            <th colspan="2">
	                <span class="title"><?= Yii::t("circuits", "Marker type"); ?></span>
	            </th>
	        </tr>
	    </thead>
		<tbody>
		    <tr>
		        <td>
			    	<input id="marker-type-network" type="radio" name="marker-type" value="net" checked></input>
			    	<label for="marker-type-network"> <?= Yii::t("circuits", "Network"); ?></label>
			    	<input id="marker-type-device" type="radio" name="marker-type" value="dev"></input>
			    	<label for="marker-type-device"> <?= Yii::t("circuits", "Device"); ?></label>
			    </td>
		    </tr>
		</tbody>
	</table>
</div>

<label id="domains-list" hidden><?= json_encode($domains); ?></label>

