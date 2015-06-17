<?php 
	use app\modules\topology\assets\ViewerAsset;
	use app\modules\circuits\assets\GoogleMapsAsset;
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

<div id="reservation-view-subtab-points" class="tab_subcontent" style="float: right; padding-left:2px;">
</div>

