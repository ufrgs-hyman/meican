<?php 

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;

    use meican\topology\models\Service;
    use meican\topology\models\TopologySynchronizer;
    use meican\topology\assets\sync\SyncFormAsset;

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
       <?= $form->field($model,'protocol')->dropDownList(ArrayHelper::map(TopologySynchronizer::getProtocols(), 'id', 'name')); ?>
    </div>

    <div class="form input">
       <?= $form->field($model,'type')->dropDownList(ArrayHelper::map(TopologySynchronizer::getTypes(), 'id', 'name')); ?>
    </div>

    <div id="subscribed-row" class="form input" <?= ($model->type == Service::TYPE_NSI_DS_1_0) ? "" : "disabled" ?>>
        <?= $form->field($model,'subscribe_enabled')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>Yii::t("topology", 'Disabled')],['id'=>true,'name'=>Yii::t("topology", 'Enabled')]], 'id', 'name'), 
                ['disabled'=>($model->protocol == Service::TYPE_NSI_DS_1_0) ? false : true]); ?>
    </div>

    <div class="form input">
        <?php echo $form->field($model,'freq_enabled')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>Yii::t("topology", 'Disabled')],['id'=>true,'name'=>Yii::t("topology", 'Enabled')]], 'id', 'name'));
            echo '<a id="cron-open-link" style="float: left;
    width: 130px;
    margin-left: 0px;
    margin-right: 10px;
    text-align: right;
    font-size: 100%;" href="#">'.Yii::t("topology", "Set recurrence").'</a>'; ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'auto_apply')->dropDownList(ArrayHelper::map(
            [['id'=>false, 'name'=>Yii::t("topology", 'Manually')],['id'=>true,'name'=>Yii::t("topology", 'Automatically')]], 'id', 'name')); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'url')->textInput(['size'=>50]); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'freq')->hiddenInput()->label(""); ?>
    </div>

    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
        <a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
    </div>

    
<?php ActiveForm::end(); ?>

<div id="cron-dialog" hidden>
    <div class="label-description" id="cron-widget"></div>
</div>
