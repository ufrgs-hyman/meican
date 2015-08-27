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

<div id="search-row" style="margin-left: 15px; margin-top:15px;" hidden>
    <input type="text" id="search-box" size="40">
    <button id="search-button"><span class="ui-icon-to-button-without-background ui-icon ui-icon-search" style="margin-left: 35%;"></span></button>
</div>

<div id="map-type-box" style="margin-left: 10px; margin-top:15px;" hidden>
    <select id="map-type-select" style="width: 85px;">
      <option value="r">Map</option>
      <option value="cr">Clean</option>
      <option value="t">Terrain</option>
      <option value="s">Satellite</option>
      <option value="h">Hybrid</option>
    </select>
</div>

<div id="marker-type-box" style="margin-left: 10px; margin-top:15px;" hidden>
    <select id="marker-type-select" style="width: 90px;">
      <option value="dev">Devices</option>
      <option value="net">Networks</option>
    </select>
</div>

<div id="refresh-box" style="margin-left: 10px; margin-top:15px;" hidden>
    <button id="refresh-button">Refresh</button>
</div>


<label id="domains-list" hidden><?= json_encode($domains); ?></label>
