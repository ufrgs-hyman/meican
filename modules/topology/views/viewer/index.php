<?php 
	use app\modules\topology\assets\ViewerAsset;
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\helpers\Url;
	use yii\widgets\Pjax;
	
	ViewerAsset::register($this);
?>

<div id="search-row" style="margin-left: 15px; margin-top:15px;" hidden>
    <input type="text" id="search-box" size="40">
    <button id="search-button"><span class="ui-icon-to-button-without-background ui-icon ui-icon-search" style="margin-left: 35%;"></span></button>
</div>

<?= $this->render('//_mapSelects'); ?>

<div id="refresh-box" style="margin-left: 10px; margin-top:15px;" hidden>
    <button id="refresh-button">Refresh</button>
</div>


<label id="domains-list" hidden><?= json_encode($domains); ?></label>
