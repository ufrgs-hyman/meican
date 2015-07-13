<?php 

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;

    use app\models\Provider;
?>

<?php $form= ActiveForm::begin([
    'id'=>'provider-form',
    'method' => 'post',
    'enableClientValidation' => false,
]); ?>
    <h4>
    <font color="#3a5879">

    <div class="form input">
        <?= $form->field($model,'name')->textInput(['size'=>50]); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'nsa')->textInput(['size'=>50]); ?>
    </div>
    
    <div class="form input">
       <?= $form->field($model,'type')->dropDownList(ArrayHelper::map(Provider::getTypes(), 'id', 'name')); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'latitude')->textInput(['size'=>20]); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'longitude')->textInput(['size'=>20]); ?>
    </div>
    
    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
        <a href="<?= Url::toRoute(['index']);?>"><?= Html::Button(Yii::t("topology", 'Cancel')); ?></a>
    </div>

    
<?php ActiveForm::end(); ?>
