<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$form=ActiveForm::begin(array(
	'enableAjaxValidation'=>false,
)); ?>

	<div class="formAccessControl input">
		<?= $form->field($group,'name'); ?>
	</div><br>
	
	<div>
		<table class="listPermissions">
	        <thead>
	            <tr>
	                <th><?= Yii::t("aaa", 'Modules'); ?></th>
	                <th><?= Yii::t("aaa", 'Create'); ?></th>
	                <th><?= Yii::t("aaa", 'Read'); ?></th>
	                <th><?= Yii::t("aaa", 'Update'); ?></th>
	                <th><?= Yii::t("aaa", 'Delete'); ?></th>
	            </tr>
	        </thead>
	
	        <tbody>
	        	<?php 	
	        		//Set table apps (check value in case of edition)
	        		$table = '';
					
	        		foreach($apps as $appValue=>$appName) {
	        			$table .= "<tr>";
	        			$table .= "<td>$appName</td>";
	        			
	        			//Capitalize first letter
	        			$appValue = ucfirst($appValue);
	        			
        				if(isset($childsChecked) && in_array("create$appValue", $childsChecked)) {
        					$table .= "<td><input name='Permissions[]' value='create$appValue' type='checkbox' checked></td>";
        				}
        				else {
        					$table .= "<td><input name='Permissions[]' value='create$appValue' type='checkbox'></td>";
        				}
        				
	        			if(isset($childsChecked) && in_array("read$appValue", $childsChecked)) {
        					$table .= "<td><input name='Permissions[]' value='read$appValue' type='checkbox' checked></td>";
        				}
        				else {
        					$table .= "<td><input name='Permissions[]' value='read$appValue' type='checkbox'></td>";
        				}
        				
	        			if(isset($childsChecked) && in_array("update$appValue", $childsChecked)) {
        					$table .= "<td><input name='Permissions[]' value='update$appValue' type='checkbox' checked></td>";
        				}
        				else {
        					$table .= "<td><input name='Permissions[]' value='update$appValue' type='checkbox'></td>";
        				}
        				
	        			if(isset($childsChecked) && in_array("delete$appValue", $childsChecked)) {
        					$table .= "<td><input name='Permissions[]' value='delete$appValue' type='checkbox' checked></td>";
        				}
        				else {
        					$table .= "<td><input name='Permissions[]' value='delete$appValue' type='checkbox'></td>";
        				}
        				
	        			$table .= "</tr>";	
        			}
        			
        			echo $table;
	        	?>
	        </tbody>
	
	    </table>
	</div>

	<div class="buttonsFormAccessControl">
		<?= Html::submitButton(Yii::t("aaa", 'Save')); ?>
		<a href="<?= Url::toRoute('index');?>"><?= Html::Button(Yii::t("aaa", 'Cancel')); ?></a>
	</div>

<?php ActiveForm::end(); ?>