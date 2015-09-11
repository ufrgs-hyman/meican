<?php 

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;

    use app\models\Service;
?>

<?php $form= ActiveForm::begin([
    'id'=>'service-form',
    'method' => 'post',
    'enableClientValidation' => false,
]); ?>
    <h4>
    <font color="#3a5879">

    <div class="form input">
       <?= $form->field($model,'type')->dropDownList(ArrayHelper::map(Service::getTypes(), 'id', 'name')); ?>
    </div>

    <div class="form input">
        <?= $form->field($model,'url')->textInput(['size'=>80]); ?>
    </div>

    </font>
    </h4>

    <div class="buttonsForm">
        <?= Html::submitButton(Yii::t("topology", 'Save')); ?>
    </div>

    
<?php ActiveForm::end(); ?>
