<?php 

use yii\helpers\Html;

?>

<div class="controls">
	<?=
		//$module = ucfirst($this->module->getName());

		//if(Yii::app()->user->checkAccess('create'.$module)) {
			Html::a(Yii::t('init', 'Add'), array('create')); 
		//}
	?>
	<?=
		Html::submitButton(Yii::t('init', 'Delete'), ['id'=>'deleteButton',]); 	
	?>
</div>

<div style="clear: both"></div>