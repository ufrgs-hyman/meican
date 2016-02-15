<?php 

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use meican\aaa\models\UserDomainRole;
use meican\topology\models\Domain;

?>

<h1><?= Yii::t('aaa', 'Federation Configuration'); ?></h1>

<?php $form= ActiveForm::begin([
    'id'=>'config-form',
    'method' => 'post',
    'enableClientValidation' => false,
]); ?>

    <h4>
    <font color="#3a5879">
    
    <div class="form input">
        <?= $form->field($model,'status')->dropDownList(ArrayHelper::map([['id'=>'true','name'=>Yii::t("aaa" , "Enabled")],['id'=>'false', 'name'=>Yii::t("aaa" , "Disabled")]], 'id', 'name')); ?>
    </div>
   
    <div class="form input">
        <?= $form->field($model,'group')->dropDownList(ArrayHelper::map(UserDomainRole::getDomainGroups(), 'role_name', 'name')); ?>
    </div>
    
	<div class="form input">
		<?= $form->field($model,'domain')->dropDownList(array_merge([null=>Yii::t("aaa" , "any")], ArrayHelper::map(Domain::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'name', 'name'))); ?>
	</div>	

    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
        <a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
    </div>

    
<?php ActiveForm::end(); ?>
