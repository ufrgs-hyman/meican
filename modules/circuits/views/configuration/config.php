<?php 

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use meican\models\Provider;

    use meican\modules\circuits\models\Protocol;

    use meican\modules\circuits\assets\ConfigurationAsset;
    ConfigurationAsset::register($this);

?>

<h1><?= Yii::t('circuits', 'Configuration'); ?></h1>

<?php $form= ActiveForm::begin([
    'id'=>'config-form',
    'method' => 'post',
    'enableClientValidation' => false,
]); ?>
    <h4>
    <font color="#3a5879">

    <div class="form input">
        <?= $form->field($model,'meicanNsa')->textInput(['size'=>50]); ?>
    </div>
   
    <div class="form input" style="margin-bottom: 15px">
        <?= $form->field($model,'protocol')->dropDownList(ArrayHelper::map(Protocol::getTypes(), 'id', 'name')); ?>
    </div>

    </font>
    </h4>
    <h1><?= Yii::t('circuits', 'NSI Connection Service'); ?> <?= Html::img('@web/images/edit_1.png', ['id'=>"default-cs"]); ?></h1>
    <h4>
    <font color="#3a5879">
    <div class="form input">
        <?= $form->field($model,'defaultProviderNsa')->textInput(['size'=>50]); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'defaultCSUrl')->textInput(['size'=>50]); ?>
    </div>

    <div class="form input" disabled>
        <?= $form->field($model,'uniportsEnabled')->dropDownList(ArrayHelper::map([['id'=>'false', 'name'=>Yii::t('circuits', 'Disabled')],['id'=>'true','name'=>Yii::t('circuits', 'Enabled')]], 'id', 'name'), ['disabled'=>true]); ?>
    </div>

    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
        <a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
    </div>

    
<?php ActiveForm::end(); ?>
