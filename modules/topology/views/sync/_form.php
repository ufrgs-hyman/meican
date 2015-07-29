<?php 

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;

    use app\models\TopologySynchronizer;
?>

<?php $form= ActiveForm::begin([
    'id'=>'service-form',
    'method' => 'post',
    'enableClientValidation' => false,
]); ?>
    <h4>
    <font color="#3a5879">

    <div class="form input">
        <?= $form->field($model,'name')->textInput(['size'=>50]); ?>
    </div>

    <div class="form input">
       <?= $form->field($model,'type')->dropDownList(ArrayHelper::map(TopologySynchronizer::getTypes(), 'id', 'name')); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'url')->textInput(['size'=>80]); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'enabled')->dropDownList(ArrayHelper::map(
            [['id'=>0, 'name'=>'Disabled'],['id'=>1,'name'=>'Enabled']], 'id', 'name')); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'auto_apply')->dropDownList(ArrayHelper::map([['id'=>0, 'name'=>'Disabled'],['id'=>1,'name'=>'Enabled']], 'id', 'name')); ?>
    </div>

    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
        <a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
    </div>

    
<?php ActiveForm::end(); ?>
