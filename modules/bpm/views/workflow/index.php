<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\data\ActiveDataProvider;
use meican\base\components\LinkColumn;
use meican\bpm\models\BpmWorkflow;

use yii\helpers\ArrayHelper;

use meican\bpm\assets\IndexAsset;
IndexAsset::register($this);

use yii\helpers\Url;

$this->params['header'] = ["Workflows", ['Home', 'Workflows']];

?>

<?= Html::csrfMetaTags() ?>

<div class="box box-default">
    <div class="box-body">               
	<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'dataProvider' => $data,
			'filterModel' => $searchModel,
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' => 'gridDevices',
			'layout' => "{items}{summary}{pager}",
			'columns' => array(
					[
						'format' => 'raw',
						'value' => function ($work){
							return Html::a(Html::tag('span', '', ['class' => 'fa fa-trash', 'title' => Yii::t("bpm", 'Delete Workflow'), 'onclick' => "deleteWorkflow($work->id)"]));
						},
						'contentOptions'=>['style'=>'cursor: pointer;'],
						'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'format' => 'raw',
						'value' => function($work){
							$href = Url::toRoute(['/bpm/workflow/viewer', 'id'=>$work->id]);
							return Html::a(Html::tag('span', '', ['class' => 'fa fa-eye', 'title' => Yii::t("bpm", 'Update Workflow')]), $href);
						},
						'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'format' => 'raw',
						'value' => function ($work){
							if(!$work->isDisabled()) return Html::a(Html::tag('span', '', ['class' => 'fa fa-pencil', 'title' => Yii::t("bpm", 'Update Workflow')]));
							else return Html::a(Html::tag('span', '', ['class' => 'fa fa-pencil', 'title' => Yii::t("bpm", 'Update Workflow'), 'onclick' => "update($work->id)"]));
						},
						'contentOptions' => function ($work){
							return ['style'=>'display: table-cell;', 'disabled'=>!$work->isDisabled(), 'class'=>'btn'];
			        	},
			        	'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'format' => 'raw',
						'value' => function($work){
							$href = Url::toRoute(['/bpm/workflow/copy', 'id'=>$work->id]);
							return Html::a(Html::tag('span', '', ['class' => 'fa fa-copy', 'title' => Yii::t("bpm", 'Update Workflow')]), $href);
						},
						'headerOptions'=>['style'=>'width: 2%;'],
					],
			        [
						'label' => Yii::t("bpm", 'Name'),
						'value' => 'name',
			        	'headerOptions'=>['style'=>'width: 35%;'],
			        ],
					[
						'label' => Yii::t("bpm", 'Domain'),
						'value' => 'domain',
						'filter' => Html::activeDropDownList($searchModel, 'domain',
								ArrayHelper::map(
									BpmWorkflow::find()->select(["domain"])->distinct(true)->orderBy(['domain'=>SORT_ASC])->asArray()->all(), 'domain', 'domain'),
								['class'=>'form-control','prompt' => Yii::t("bpm", 'any')]
						),
						'headerOptions'=>['style'=>'width: 35%;'],
					],
					[
						'format' => 'raw',
						'label' => 'Status',
						'value' => function ($work){
							return Html::input('checkbox', '', $work->id, ['id'=>'toggle-'.$work->id, 'class'=>'toggle-event-class', 'checked'=>!$work->isDisabled(), 'data-toggle'=>"toggle", 'data-size'=> "mini", "data-on" => Yii::t("bpm", "Enabled"), "data-off"=> Yii::t("bpm", "Disabled"), "data-width"=>"100", "data-onstyle"=>"success", "data-offstyle"=>"warning"]);
						},
						'filter' => Html::activeDropDownList($searchModel, 'status',
								["enabled" => Yii::t("bpm", "Enabled"), "disabled"=> Yii::t("bpm", "Disabled")],
								['class'=>'form-control','prompt' => Yii::t("circuits", 'any')]
						),
						'headerOptions'=>['style'=>'width: 18%;'],
					],
					
				),
		]);
	?>
	</div>
</div>

<?php 

Modal::begin([
    'id' => 'dialog',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-btn" class="btn btn-default">'.Yii::t("bpm", "Cancel").'</button><button id="delete-btn" class="btn btn-danger">'.Yii::t("bpm", "Delete").'</button><button id="ok-btn" class="btn btn-primary">Ok</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;" id="message"></p>';

Modal::end(); 

?>