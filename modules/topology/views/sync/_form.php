<?php 

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;

    use app\models\Service;
    use app\models\TopologySynchronizer;
    use app\modules\topology\assets\SyncFormAsset;

    SyncFormAsset::register($this);
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

    <div id="subscribed-row" class="form input" <?= ($model->type == Service::TYPE_NSI_DS_1_0) ? "" : "hidden" ?>>
        <?= $form->field($model,'subscribed')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>'Disabled'],['id'=>true,'name'=>'Enabled']], 'id', 'name')); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'url')->textInput(['size'=>80]); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'auto_apply')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>'Disabled'],['id'=>true,'name'=>'Enabled']], 'id', 'name')); ?>
    </div>

    <div id="changePasswordOption">
        <?= $form->field($model, 'freq_enabled')->checkBox(); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'freq')->hiddenInput()->label(""); ?>
    </div>
        
    <div id="cron-freq" <?= $model->freq_enabled ? "" : "hidden" ?>>
    </div>

    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
        <a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
    </div>

    
<?php ActiveForm::end(); ?>
