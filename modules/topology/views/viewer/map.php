<?php 
	use meican\modules\topology\assets\ViewerAsset;
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\helpers\Url;
	use yii\widgets\Pjax;
	
	ViewerAsset::register($this);
?>

<?= $this->render('//_viewerMenu'); ?>
