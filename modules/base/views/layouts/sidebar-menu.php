<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use meican\base\widgets\Menu;
use meican\aaa\RbacController;

echo Menu::widget([
    'options' => ['class' => 'sidebar-menu'],
    'items' => [
        [
            'label' => Yii::t('home','Dashboard'), 
            'url' => ['/home'],
            'icon' => 'fa fa-home'
        ],
        [
            'label' => Yii::t('circuits','Circuits'),
            'url' => '#',
            'icon' => 'fa fa-calendar',
            'items' => [
                ['label'=>Yii::t('circuits','Reserve'), 'url'=>['/circuits/reservation/create']],
                ['label'=>Yii::t('circuits','Status'),'url'=>['/circuits/reservation/status']],
                ['label'=>Yii::t('circuits','Authorization'),'url'=>['/circuits/authorization']],
                ['label'=>Yii::t('circuits','Configuration'),'url'=>['/circuits/config'], 'visible'=>RbacController::can('configuration/read')]
            ]
        ],
        [
            'label'=>Yii::t('bpm','Workflows'),
            'url' => '#',
            'icon' => 'fa fa-random',
            'items'=>[
                ['label'=>Yii::t('bpm','Create'), 'url'=>['/bpm/workflow/new'], 'visible'=> RbacController::can('workflow/create')],
                ['label'=>Yii::t('bpm','Status'), 'url'=>['/bpm/workflow']],
            ],
            'visible'=> RbacController::can('workflow/read')
        ],
        // [
        //     'label'=>Yii::t('monitoring','Monitoring'),
        //     'url' => '#',
        //     'icon' => 'fa fa-area-chart',
        //     'items'=>[
        //         ['label'=>Yii::t('bpm','Traffic'), 'url'=>['/monitoring']],
        //     ],
        // ],
        [
            'label'=>Yii::t('topology','Topology'),
            'url' => '#',
            'icon' => 'fa fa-globe',
            'items'=>[
                ['label'=>Yii::t('topology','Domains'), 'url'=>['/topology/domain'], 'visible'=>RbacController::can('domain/read')],
                ['label'=>Yii::t('topology','Providers'), 'url'=>['/topology/provider'], 'visible'=>RbacController::can('domain/read')],
                ['label'=>Yii::t('topology','Networks'), 'url'=>['/topology/network'], 'visible'=>RbacController::can('domainTopology/read')],
                #['label'=>Yii::t('topology','Ports'), 'url'=>['/topology/port'], 'visible'=>RbacController::can('domainTopology/read')],
                ['label'=>Yii::t('topology','Viewer'), 'url'=>['/topology/viewer'], 'visible'=>(RbacController::can("domainTopology/read") || RbacController::can("domain/read"))],
                ['label'=>Yii::t('topology','Discovery'), 'url'=>['/topology/discovery']],
            ],
            'visible'=>(RbacController::can('domainTopology/read') || RbacController::can('discovery/read'))
        ],
        [
            'label'=>Yii::t('aaa','Tests'),
            'url' => '#',
            'icon' => 'fa fa-calendar-check-o',
            'items'=>[
                    ['label'=>Yii::t('topology','Create'),'url'=>['/tester/manager/create'], 'visible'=>RbacController::can('test/create')],
                    ['label'=>Yii::t('topology','Status'),'url'=>['/tester'], 'visible'=>RbacController::can('test/read')]
            ],
            'visible'=>(RbacController::can('test/read') || RbacController::can('test/create'))
        ],
        [
            'label'=>Yii::t('aaa','Users'),
            'url' => '#',
            'icon' => 'fa fa-users',
            'items'=>[
                    ['label'=>Yii::t('aaa','Users'), 'url'=>['/aaa/user'], 'visible'=>(RbacController::can('user/read') || RbacController::can('role/read'))],
                    ['label'=>Yii::t('aaa','Groups'), 'url'=>['/aaa/group'], 'visible'=>RbacController::can('group/read')],
                    ['label'=>Yii::t('aaa','Configuration'), 'url'=>['/aaa/config'], 'visible'=>RbacController::can('group/update')],
            ],
        ],
    ],
]);

?>