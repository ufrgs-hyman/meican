<?php 
use app\components\widgets\MenuWidget;
use app\controllers\RbacController;

echo MenuWidget::widget([
    'options' => ['class' => 'sidebar-menu'],
    'items' => [
        [
            'label' => Yii::t('init','Dashboard'), 
            'url' => ['/init'],
            'icon' => 'fa fa-home'
        ],
        [
            'label' => Yii::t('circuits','Reservations'),
            'url' => '#',
            'icon' => 'fa fa-calendar',
            'items' => [
                ['label'=>Yii::t('circuits','Create'), 'url'=>['/circuits/reservation/create']],
                ['label'=>Yii::t('circuits','Status'),'url'=>['/circuits/reservation/status']],
                ['label'=>Yii::t('circuits','History'),'url'=>['/circuits/reservation/history']],
                ['label'=>Yii::t('circuits','Authorization'),'url'=>['/circuits/authorization/index']],
                ['label'=>Yii::t('circuits','Configuration'),'url'=>['/circuits/configuration/index'], 'visible'=>RbacController::can('configuration/read')]
            ]
        ],
        [
            'label'=>Yii::t('bpm','Workflows'),
            'url' => '#',
            'icon' => 'fa fa-random',
            'items'=>[
                ['label'=>Yii::t('bpm','Create'), 'url'=>['/bpm/workflow/new'], 'visible'=> RbacController::can('workflow/create')],
                ['label'=>Yii::t('bpm','Status'), 'url'=>['/bpm/workflow/index']],
            ],
            'visible'=> RbacController::can('workflow/read')
        ],
        [
            'label'=>Yii::t('topology','Topologies'),
            'url' => '#',
            'icon' => 'fa fa-globe',
            'items'=>[
                ['label'=>Yii::t('topology','Domains'), 'url'=>['/topology/domain/index'], 'visible'=>RbacController::can('domain/read')],
                ['label'=>Yii::t('topology','Providers'), 'url'=>['/topology/provider/index'], 'visible'=>RbacController::can('domain/read')],
                ['label'=>Yii::t('topology','Networks'), 'url'=>['/topology/network/index'], 'visible'=>RbacController::can('domainTopology/read')],
                ['label'=>Yii::t('topology','Devices'), 'url'=>['/topology/device/index'], 'visible'=>RbacController::can('domainTopology/read')],
                ['label'=>Yii::t('topology','Ports'), 'url'=>['/topology/port/index'], 'visible'=>RbacController::can('domainTopology/read')],
                ['label'=>Yii::t('topology','Viewer'), 'url'=>['/topology/viewer/index'], 'visible'=>(RbacController::can("domainTopology/read") || RbacController::can("domain/read"))],
                ['label'=>Yii::t('topology','Synchronizer'), 'url'=>['/topology/sync/index'], 'visible'=>RbacController::can('synchronizer/read')],
                ['label'=>Yii::t('topology','Changes'), 'url'=>['/topology/change/applied'], 'visible'=>RbacController::can('synchronizer/read')],
            ],
            'visible'=>(RbacController::can('domainTopology/read') || RbacController::can('synchronizer/read'))
        ],
        [
            'label'=>Yii::t('aaa','Automated Tests'),
            'url' => '#',
            'icon' => 'fa fa-calendar-check-o',
            'items'=>[
                    ['label'=>Yii::t('topology','Create'),'url'=>['/circuits/automated-test/create'], 'visible'=>RbacController::can('test/create')],
                    ['label'=>Yii::t('topology','Status'),'url'=>['/circuits/automated-test/index'], 'visible'=>RbacController::can('test/read')]
            ],
            'visible'=>(RbacController::can('test/read') || RbacController::can('test/create'))
        ],
        [
            'label'=>Yii::t('aaa','Users'),
            'url' => '#',
            'icon' => 'fa fa-users',
            'items'=>[
                    ['label'=>Yii::t('aaa','Users'), 'url'=>['/aaa/user/index'], 'visible'=>(RbacController::can('user/read') || RbacController::can('role/read'))],
                    ['label'=>Yii::t('aaa','Groups'), 'url'=>['/aaa/group/index'], 'visible'=>RbacController::can('group/read')],
                    ['label'=>Yii::t('aaa','Configuration'), 'url'=>['/aaa/configuration/index'], 'visible'=>RbacController::can('group/update')],
            ],
        ],
        [
            'label'=>Yii::t('init','External Access'),
            'url' => "#",
            'icon' => 'fa fa-external-link',
            'items'=>[
                [
                    'label'=>Yii::t('init','Console Central'), 
                    'url'=>'http://monitora.cipo.rnp.br/console/',
                ],
                [
                    'label'=>Yii::t('init','Monitoring'),
                    'url'=>'http://monitora.cipo.rnp.br/', 
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