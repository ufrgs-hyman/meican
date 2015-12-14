<?php
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Html;
use app\modules\aaa\assets\AccountAsset;

AccountAsset::register($this);

$form=ActiveForm::begin(array(
	'id'=>'userSettings-form',
	'enableClientValidation'=>false,
		'action'=>'account',
)); ?>       
    <div id="settingsWrapper" style="float: left; margin-right: 5%;"> 
		<h1><?= Yii::t('aaa', 'My account'); ?></h1>    
		
		    <div style="margin-left: 45%;">
		            <img src="<?= $avatarUrl; ?>">
		    </div>
		    
		    <h4>
			<font color="#3a5879">
			        <?= $form->field($user, 'login')->textInput(['hidden' => true])->label(''); ?>
			        <div class="form input">
			        <?= $form->field($user, 'login')->textInput(['disabled' => true]); ?>
			        </div>
			        <div class="form input">
					<?= $form->field($user, 'name'); ?>
					</div>
					<div class="form input">
			        <?= $form->field($user, 'email'); ?>  
			        </div>  
			        
			        <div id="changePasswordOption">
						<?= $form->field($user, 'isChangedPass')->checkBox(); ?>
					</div>
		
	        <div id="changePasswordForm">
					<div class="form input">
						<?= $form->field($user, 'currentPass')->passwordInput(); ?>
					</div>
				
					<div class="form input">
						<?= $form->field($user, 'newPass')->passwordInput(); ?>
					</div>
					
					<div class="form input">
						<?= $form->field($user, 'newPassConfirm')->passwordInput(); ?>
					</div>
	        </div>   
		
			</font> 
			</h4>
			
			<div class="buttonsForm">
           		<input type="submit" class="save" value="<?= Yii::t('aaa', 'Save'); ?>"/>
        	</div> 
			
	</div>
			
	<div style="float: left;">   
		<h1><?= Yii::t('aaa', 'My preferences'); ?></h1>        

		<h4>
		<font color="#3a5879">
			<div class="form input">
			    <?= $form->field($user, 'language')->dropDownList(
              		array('en-US' => Yii::t('aaa', 'English'), 'pt-BR' => Yii::t('aaa', 'Portuguese')));
              	?>
			</div>
			
			<div class="form input">
		        <?php 	
		        	$zones = DateTimeZone::listIdentifiers();
		        	foreach ($zones as &$zone) {
		        		$zone = str_replace("_", " ", $zone);
		        	}

		        	echo $form->field($user, 'timeZone')->dropDownList(array_combine(
			        	DateTimeZone::listIdentifiers(),$zones))->label(Yii::t('aaa', 'Time Zone'));
              	?>                	
			</div>
			
			<div class="form input">
			        <?= $form->field($user, 'dateFormat')->dropDownList(
			        		array(
							'dd/MM/yyyy HH:mm' => Yii::t('aaa', 'dd/mm/yyyy'), 
							'MM/dd/yyyy HH:mm' => Yii::t('aaa', 'mm/dd/yyyy'), 
							'yyyy/MM/dd HH:mm' => Yii::t('aaa', 'yyyy/mm/dd')));
              	?>              	
			</div>
		</font> 
		</h4>
		
	</div>
			
            

<?php ActiveForm::end(); ?>