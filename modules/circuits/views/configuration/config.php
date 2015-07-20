<?php 

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use app\models\Provider;

    use app\modules\circuits\assets\ConfigurationAsset;
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
        <?= $form->field($model,'protocol')->dropDownList(ArrayHelper::map([['id'=>null, 'name'=>"NSI Connection Service"]], 'id', 'name')); ?>
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
        <?= $form->field($model,'uniportsEnabled')->dropDownList(ArrayHelper::map([['id'=>'false', 'name'=>"Disabled"],['id'=>'true','name'=>'Enabled']], 'id', 'name'), ['disabled'=>true]); ?>
    </div>

    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
        <a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
    </div>

    
<?php ActiveForm::end(); ?>
