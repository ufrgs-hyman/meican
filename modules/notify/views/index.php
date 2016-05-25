<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\i18n\Formatter;

use meican\notify\models\Notification;
?>


<?= Html::csrfMetaTags() ?>


<?= GridView::widget([
					'options' => ['class' => 'list'],
					'dataProvider' => $data,
					'formatter' => new Formatter(['nullDisplay'=>'']),
					'id' => 'gridRequest',
					'layout' => "{items}{pager}",
					'rowOptions' => function ($model, $key, $index, $grid){
					},
					'columns' => array(
							[
								'format' => 'raw',
								'value' => function ($noti){
									return Notification::makeHtmlNotificationAuth($noti);
								},
							],
					),
					]);
?>
	