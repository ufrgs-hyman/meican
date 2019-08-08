<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */
 
use meican\base\grid\Grid;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use meican\base\grid\IcheckboxColumn;
use meican\base\widgets\GridButtons;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\i18n\Formatter;
use yii\bootstrap\Modal;

use meican\topology\models\Port;
use meican\base\components\LinkColumn;
use meican\topology\assets\port\IndexAsset;

IndexAsset::register($this);

$this->params['header'] = [Yii::t('topology', 'Ports'), [Yii::t('home', 'Home'), Yii::t('topology', 'Topology'), 'Ports']];

?>

<?= Html::csrfMetaTags() ?>

<script>
	var selected_domain = <?php echo json_encode($selected_domain); ?>;
</script>

<?php
	foreach ($domains as $dom):
	if(!$selected_domain) $selected_domain = $dom->id;
	if($dom->id != $selected_domain) echo '<div id="box-dom-'.$dom->id.'" class="box box-default collapsed-box">';
	else echo '<div id="box-dom-'.$dom->id.'" class="box box-default">';
	?>

		<div class="box-header with-border">
        	<h3 class="box-title"><?php
	        	$text = Yii::t('topology', 'Domain')." - ";
	            $text .= ($dom->name);
	            echo $text;
	        ?></h3>

            <div class="box-tools pull-right">
            	<?php if($dom->id != $selected_domain) echo '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>';
            	else echo '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>'
            	?>
            </div>
        </div>

        <div class="box-body">
		    <div>
            	<a id="add-port-grid-btn" value=<?= $dom->id;?> class="btn btn-primary btn-add"><?= Yii::t('topology', 'Add')?></a>
            	<a id="delete-port-grid-btn" class="btn btn-default btn-delete" value=<?= $dom->id;?>><?= Yii::t('topology', 'Delete')?></a>
        	</div><br>

			<?php $form = ActiveForm::begin([
	            'method' => 'post',
	            'action' => ['/topology/port/delete'],
	            'id' => 'port-grid-form-'.$dom->id,  
	        ]); ?>
	
			<?=
			Grid::widget([
				'id' =>'grid'.$dom->id,
				'emptyText' => Yii::t('topology', 'No Ports added to this domain'),
				'dataProvider' => new ArrayDataProvider([
					'models' => $dom->getBiPorts(),
					'key' => 'id',
					'pagination' => false,
				]),
				'layout' => '{items}',
				'columns' => array(
					[
						'class'=>ICheckboxColumn::className(),
						'checkboxOptions' =>['class'=>'deleteUrn'.$dom->id],
						'multiple'=>false,
						'name'=>'delete',
						'headerOptions'=>['style'=>'width: 2%;'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{edit}',
						'contentOptions' => function($port){
							return ['class'=>'btn-edit', 'id' => $port->id, 'value' => $port->getNetwork()->one()->getDomain()->one()->id];
						},
						'buttons' => [
							'edit' => function ($url, $model) {
								return Html::a('<span class="fa fa-pencil"></span>', null);
							}
						],
					],
					[
						'format' => 'raw',
						'label' => Yii::t('topology', 'Network'),
						'value' => function($port){
							return $port->getNetwork()->one()->name;
						},
						'headerOptions'=>['style'=>'width: 18%;'],
					],
					[
						'label' => Yii::t('topology', 'Name'),
						'value' => 'name',
						'headerOptions'=>['style'=>'width: 20%;'],
					],
					[
						'format' => 'raw',
						'label' => Yii::t('topology', 'VLANs'),
						'value' => function ($port){
							if($port->vlan_range) return $port->vlan_range;
							return $port->getInboundPortVlanRange();
						},
						'headerOptions'=>['style'=>'width: 13%;'],
					],
					[
						'label' => Yii::t('topology', 'Capacity (Mbps)'),
						'value' => 'capacity',
						'headerOptions'=>['style'=>'width: 13%;'],
					],
					[
						'label' => Yii::t('topology', 'Reservable Capacity (Mbps)'),
						'value' => 'max_capacity',
						'headerOptions'=>['style'=>'width: 20%;'],
					],
					[
						'label' => Yii::t('topology', 'Granularity (Mbps)'),
						'value' => 'granularity',
						'headerOptions'=>['style'=>'width: 15%;'],
					],
				),
			]);
			?>
			
			<?php ActiveForm::end(); ?>
        </div>
    </div>
<?php endforeach; ?>

<?php 

Modal::begin([
    'id' => 'dialog',
    'footer' => '<button id="close-btn" class="btn btn-default" data-dismiss="modal">Ok</button>',
]);

echo '<p style="text-align: left; height: 100%; width:100%;" id="message"></p>';

Modal::end(); 

Modal::begin([
		'id' => 'delete-port-modal',
		'footer' => '<button id="cancel-btn" class="btn btn-default">'.Yii::t('topology', 'Cancel').'</button> <button id="delete-port-btn" class="grid-btn btn btn-danger">'.Yii::t('topology', 'Delete').'</button>',
]);

echo Yii::t('topology', 'Do you want delete the selected items?');

Modal::end();

Modal::begin([
    'id' => 'add-port-modal',
    'header' => Yii::t('topology', 'Add Port'),
    'footer' => '<button id="cancel-btn" class="btn btn-default">'.Yii::t('topology', 'Cancel').'</button> <button id="save-port-btn" class="btn btn-primary">'.Yii::t('topology', 'Save').'</button>',
]);


echo '<div id="add-port-form-wrapper"></div>';

Modal::end();

Modal::begin([
	'id' => 'edit-port-modal',
	'header' => Yii::t('topology', 'Edit Port'),
	'footer' => '<button id="cancel-btn" class="btn btn-default">'.Yii::t('topology', 'Cancel').'</button> <button id="save-edit-port-btn" class="btn btn-primary">'.Yii::t('topology', 'Save').'</button>',
]);


echo '<div id="edit-port-form-wrapper"></div>';

Modal::end();
?>