<?php 
    use yii\grid\GridView;
    use yii\grid\CheckboxColumn;
    use yii\data\ActiveDataProvider;
    use yii\helpers\Html;
    use yii\helpers\ArrayHelper;
    use yii\widgets\ActiveForm;
    use yii\widgets\Pjax;

    use meican\topology\models\TopologyChange;
    use meican\topology\models\Domain;
    use meican\base\components\LinkColumn;
?>

<h1><?= Yii::t('topology',"Applied changes") ?></h1>

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
                [
                    'attribute' => 'applied_at',
                    'format' => 'datetime',
                    'filter' => false,
                    'headerOptions'=>['style'=>'width: 9%;'],
                ],
                [
                    'attribute' => 'domain',
                    'filter' => Html::activeDropDownList($searchModel, 'domain', 
                        ArrayHelper::map(
                            TopologyChange::find()->asArray()->select("domain")->distinct(true)->orderBy("domain ASC")->all(), 'domain', 'domain'),
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
            ),
    ]);
?>

<?php Pjax::end(); ?>
