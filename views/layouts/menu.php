<?php 
use yii\widgets\Menu;
use app\controllers\RbacController;

echo Menu::widget([
	'encodeLabels' => false,
	'items' => [
		[
			'label' => '<h3>'.Yii::t('init','Dashboard').'</h3>', 
			'url' => ['/init'],
			'linkOptions' => ['class' => 'top']
		],
		[
			'label' => '<h3><a href="#" class="top"><span class="ui-icon ui-icon-circle-arrow-s"></span>'.Yii::t('circuits','Reservations').'</a></h3>',
			'linkOptions' => ['class' => 'top'],
			'items' => [
				['label'=>Yii::t('circuits','Create'), 'url'=>['/circuits/reservation/create']],
				['label'=>Yii::t('circuits','Status'),'url'=>['/circuits/reservation/status']],
				['label'=>Yii::t('circuits','History'),'url'=>['/circuits/reservation/history']],
				['label'=>Yii::t('circuits','Authorization'),'url'=>['/circuits/authorization/index']]
			],
			'visible'=>RbacController::can('reservation/read')
		],
		[
			'label'=>'<h3><a href="#" class="top"><span class="ui-icon ui-icon-circle-arrow-s"></span>'.Yii::t('bpm','Workflows').'</a></h3>',
			'items'=>[
				['label'=>Yii::t('bpm','New'), 'url'=>['/bpm/workflow/new']],
				['label'=>Yii::t('bpm','Status'), 'url'=>['/bpm/workflow/index']],
			],
			'visible'=>RbacController::can('workflow/read')
		],
		[
			'label'=>'<h3><a href="#" class="top"><span class="ui-icon ui-icon-circle-arrow-s"></span>'.Yii::t('topology','Topologies').'</a></h3>',
			'items'=>[
				['label'=>Yii::t('topology','Domains'), 'url'=>['/topology/domain/index']],
				['label'=>Yii::t('topology','Providers'), 'url'=>['/topology/provider/index']],
				['label'=>Yii::t('topology','Networks'), 'url'=>['/topology/network/index']],
				['label'=>Yii::t('topology','Devices'), 'url'=>['/topology/device/index']],
				['label'=>Yii::t('topology','Ports'), 'url'=>['/topology/port/index']],
				['label'=>Yii::t('topology','Viewer'), 'url'=>['/topology/viewer/index']],
				['label'=>Yii::t('topology','Synchronizer'), 'url'=>['/topology/sync/index'], 'visible'=>RbacController::can('topology/update')],
				['label'=>Yii::t('topology','Changes'), 'url'=>['/topology/sync/pending-changes'], 'visible'=>RbacController::can('topology/update')],
				['label'=>Yii::t('topology','Automated Tests'),'url'=>['/circuits/automated-test/index'],'visible'=>RbacController::can('topology/update')]
			],
			'visible'=>RbacController::can('topology/read')
		],
		[
			'label'=>'<h3><a href="#" class="top"><span class="ui-icon ui-icon-circle-arrow-s"></span>'.Yii::t('aaa','Users').'</a></h3>',
			'items'=>[
					['label'=>Yii::t('aaa','Users'), 'url'=>['/aaa/user/index'], 'visible'=>RbacController::can('user/read')],
					['label'=>Yii::t('aaa','Groups'), 'url'=>['/aaa/group/index'], 'visible'=>RbacController::can('group/read')],
			],
		],
		[
			'label'=>'<h3><a href="#" class="top"><span class="ui-icon ui-icon-circle-arrow-s"></span>'.Yii::t('init','External Access').'</a></h3>',
			'items'=>[
				[
					'label'=>Yii::t('init','Console Central'), 
					'url'=>'http://logs.cipo.rnp.br/console/',
				],
				[
					'label'=>Yii::t('init','Monitoring'),
					'url'=>'http://monitoramento.cipo.rnp.br/', 
				],
				[
					'label'=>Yii::t('init','Weathermap'),
					'url'=>'http://weathermap.cipo.rnp.br/',
				],
			]
		],
	],
]);
?>