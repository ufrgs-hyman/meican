<?php 

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\modules\topology\assets\ImportAsset;

ImportAsset::register($this);

$form=ActiveForm::begin(array(
	'enableClientValidation'=>false,
)); ?>

	                <div class="form input">
	                	<h4>
						<font color="#3a5879">
				        	<label for="method"><?= $model->attributeLabels()['method']; ?></label>
				        </h4>
				        </font>
					    <div style="width: 222px; float: left; text-align: left;">
					        <input type="radio" name="ImportForm[method]" value="0" checked="checked"/>&nbsp;<?= Yii::t("topology", "Update"); ?>
					        <input type="radio" name="ImportForm[method]" value="1"/>&nbsp;<?= Yii::t("topology", "Delete current topology"); ?>
					    </div>
					</div>
					<h4>
						<font color="#3a5879">
					<div class="form input">
						<?= $form->field($model,'url')->textInput(['readonly' => true]); ?>
					</div>
					<div class="form input">
						<?= $form->field($model,'otherUrl'); ?>
					</div>
					<div class="form input">
						<?= $form->field($model,'xml')->textArea(['cols'=>40, 'rows'=>8]); ?>
					</div>
					</h4>
				        </font>
	
	                <div class="buttonsForm">
						<?= Html::submitButton(Yii::t("topology", 'Import'), ['id'=>'import-button']); ?>
					</div>
					
					<div id="loading-dialog" hidden>
					</div>

<?php ActiveForm::end(); ?>

