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

<h1 style="clear: none; float: left; z-index: 999999; position: absolute;">
	<label id="label_res_name" for="res_name" style="width: 170px;"><?= Yii::t("topology", 'Topology Viewer'); ?></label>
</h1>

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
			    	<input id="marker-type-network" type="radio" name="marker-type" value="network" checked></input>
			    	<label for="marker-type-network"> <?= Yii::t("circuits", "Network"); ?></label>
			    	<input id="marker-type-device" type="radio" name="marker-type" value="device"></input>
			    	<label for="marker-type-device"> <?= Yii::t("circuits", "Device"); ?></label>
			    </td>
		    </tr>
		</tbody>
	</table>
</div>

<label id="domains-list" hidden><?= json_encode($domains); ?></label>

