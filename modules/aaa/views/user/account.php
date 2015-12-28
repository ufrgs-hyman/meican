<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;

use meican\aaa\assets\AccountAsset;

$this->params['header'] = ["My account", ['Home', 'My account']];

AccountAsset::register($this);

$form=ActiveForm::begin(array(
	'id'=>'account-form',
	'action'=>'account',
	'layout' => 'horizontal'
)); ?>       

	<div class="box box-default">
		<div class="box-header with-border">
		  <h3 class="box-title">Profile and preferences</h3>
		</div>
		<div class="box-body">
			<div class="row">
		    	<div class="col-md-6">
		        	<?= $form->field($user, 'login')->textInput(['disabled' => true]); ?>
					<?= $form->field($user, 'name'); ?>
			        <?= $form->field($user, 'email'); ?>  
			        
			        <div id="changePasswordOption">
						<?= $form->field($user, 'isChangedPass')->checkBox(); ?>
					</div>

		    		<div id="changePasswordForm" style="display: none;">
						<?= $form->field($user, 'currentPass')->passwordInput(); ?>
				
						<?= $form->field($user, 'newPass')->passwordInput(); ?>
					
						<?= $form->field($user, 'newPassConfirm')->passwordInput(); ?>
		    		</div> 
				</div> 
				<div class="col-md-6">
		    		<?= $form->field($user, 'language')->dropDownList(
		          		array('en-US' => Yii::t('aaa', 'English'), 'pt-BR' => Yii::t('aaa', 'Portuguese')));
		          	?>
				        <?= $form->field($user, 'dateFormat')->dropDownList(
				        		array(
								'dd/MM/yyyy' => Yii::t('aaa', 'dd/mm/yyyy'), 
								'MM/dd/yyyy' => Yii::t('aaa', 'mm/dd/yyyy'), 
								'yyyy/MM/dd' => Yii::t('aaa', 'yyyy/mm/dd')));
		          	?> 
		          	<?= $form->field($user, 'timeFormat')->dropDownList(
				        		array(
								'HH:mm' => Yii::t('aaa', 'HH:mm')));
		          	?> 

		          	<?php 	
			        	$zones = DateTimeZone::listIdentifiers();
			        	foreach ($zones as &$zone) {
			        		$zone = str_replace("_", " ", $zone);
			        	}

			        	echo $form->field($user, 'timeZone')->dropDownList(array_combine(
				        	DateTimeZone::listIdentifiers(),$zones))->label(Yii::t('aaa', 'Time Zone'));
		          	?>  
		    	</div> 
			</div>
		</div>
		<div class="box-footer">
			<div class="row">
		    	<div class="col-md-6">
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-6">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</div>
	            </div>
	        </div>
		</div>
	</div>

<?php ActiveForm::end(); ?>