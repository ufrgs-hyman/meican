<?php 
    use yii\grid\GridView;
    use yii\grid\CheckboxColumn;

    use yii\data\ActiveDataProvider;

    use app\models\TopologyChange;
    use app\models\Domain;
    
    use app\components\LinkColumn;
    
    use yii\helpers\Html;
    use yii\helpers\ArrayHelper;
    
    use yii\widgets\ActiveForm;

    use yii\widgets\Pjax;

    use app\modules\topology\assets\ChangeAsset;
    
    ChangeAsset::register($this);
?>

<h1><?= "Pending Topology Changes" ?></h1>

<button style="margin-bottom:10px;" id="apply-all">Apply all changes</button>
<label id="sync-event-id" hidden><?= $eventId; ?></label>
    
<?php Pjax::begin([
    'id' => 'pjax-changes',
    'enablePushState' => false,
]); ?>

<?php 
    echo GridView::widget([
        'id' => 'grid-changes',
        'options' => ['class' => 'list'],
        'layout' => "{items}{summary}{pager}",
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'columns' => array(
                array(
                    'class'=> CheckboxColumn::className(),
                    'name'=>'delete',         
                    'checkboxOptions'=>[
                        'class'=>'deleteCheckbox',
                    ],
                    'headerOptions'=>['style'=>'width: 2%;'],
                ),
                [
                    'format' => 'raw',
                    'value' => function ($model){
                        return '<a href="#">'.Html::img('@web/images/ok.png', ['class' => "apply-button"])."</a>";
                    },
                    'headerOptions'=>['style'=>'width: 2%;'],
                ],
                [
                    'attribute' => 'domain',
                    'filter' => Html::activeDropDownList($searchModel, 'domain', 
                        ArrayHelper::map(
                            TopologyChange::find()->where(['status'=>TopologyChange::STATUS_PENDING])->asArray()->select("domain")->distinct(true)->orderBy("domain ASC")->all(), 'domain', 'domain'),
                        ['class'=>'form-control','prompt' => 'any']),
                    'headerOptions'=>['style'=>'width: 16%;'],
                ],
                [
                    'header' => Yii::t('topology', 'Type'),
                    'value' => function($model) {
                        return $model->getType();
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'type', 
                        ArrayHelper::map(
                            TopologyChange::getTypes(), 'id', 'name'),
                        ['class'=>'form-control','prompt' => 'any']),
                    'headerOptions'=>['style'=>'width: 7%;'],
                ],
                [
                    'attribute' => 'item_type',
                    'filter' => Html::activeDropDownList($searchModel, 'item_type', 
                        ArrayHelper::map(
                            TopologyChange::getItemTypes(), 'id', 'name'),
                        ['class'=>'form-control','prompt' => 'any']),
                    'value' => function($model) {
                        return $model->getItemType();
                    },
                    'headerOptions'=>['style'=>'width: 9%;'],
                ],
                [
                    'header' => Yii::t('topology', 'Parent'),
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getParentInfo();
                    },
                    'headerOptions'=>['style'=>'width: 16%;'],
                ],
                [
                    'attribute' => 'data',
                    'filter' => false,
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getDetails();
                    },
                    'headerOptions'=>['style'=>'width: 45%;'],
                ],
                [
                    'header' => '',
                    'filter' => false,
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->error ? '<label class="error" hidden>Error: '.$model->error."</label>" : "";
                    },
                    'headerOptions'=>['style'=>'width: 5%;'],
                ],
            ),
    ]);
?>

<?php Pjax::end(); ?>

<div id="loading-dialog" hidden>
