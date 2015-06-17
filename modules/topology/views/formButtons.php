<?php 
	use yii\helpers\Html;
?>

<div class="controls">
	<?=
		//$module = ucfirst($this->module->getName());

		//if(Yii::app()->user->checkAccess('create'.$module)) {
			Html::a('Add', array('create')); 
		//}
	?>
	<?=
	Html::submitButton('Delete', ['id'=>'deleteButton']);
	?>
</div>

<div style="clear: both"></div>
