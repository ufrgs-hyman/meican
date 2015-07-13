<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\grid\ActionColumn;
	use app\components\LinkColumn;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use yii\widgets\LinkPager;
	use yii\data\ActiveDataProvider;
	use yii\data\ArrayDataProvider;
	use yii\i18n\Formatter;
	use app\models\Port;
	use yii\jui\Dialog;
	
	use app\modules\topology\assets\PortAsset;
	PortAsset::register($this);
?>


<?= Html::csrfMetaTags() ?>

<script>
	var selected_domain = <?php echo json_encode($selected_domain); ?>;
</script>

<h1><?php echo Yii::t('topology', 'Ports'); ?></h1>
	
<?php foreach ($domains as $dom): ?>
    <div id="domain<?php echo $dom->id; ?>">

    	<h4><?=Html::img('@web'.'/images/minus.gif', ['id' => "collapseExpand".$dom->id]);?>
        <?php
        	$text = Yii::t('topology', 'Domain')." ";
            $text .= ($dom->name);
            echo $text;
        ?></h4>
	            
        <div id="collapsable<?php echo $dom->id ?>">                
                
        <?php \yii\widgets\Pjax::begin([
		    'id' => 'pjaxContainer'.$dom->id,
		]); ?>
	
		<?=
		GridView::widget([
			'options' => ['class' => 'list-without-margin'],
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' =>'grid'.$dom->id,
			'emptyText' => Yii::t('topology', 'No Ports added to this domain'),
			'dataProvider' => new ArrayDataProvider([
				'models' => $dom->getBiPorts(),
				'key' => 'id',
				'pagination' => false,
			]),
			'layout' => "{items}",
			'columns' => array(
				[
					'class'=>CheckboxColumn::className(),
					'name'=>'deleteUrn',
					'multiple'=>false,
					'contentOptions'=>['style'=>'width: 15px;'],
				],
				[
					'format' => 'raw',
					'value' => function ($port){
						return Html::img('@web'.'/images/edit_1.png', ['title' => Yii::t('topology', 'Update'), 'onclick' => "editPort(this, $port->id)"]);
					},
					'contentOptions'=>['style'=>'width: 15px; cursor: pointer;'],
				],
				[
					'format' => 'raw',
					'value' => function ($port){
						return Html::img('@web'.'/images/remove.png', ['title' => Yii::t('topology', 'Delete'), 'onclick' => "deletePort($port->id)"]);
					},
					'contentOptions'=>['style'=>'width: 15px; cursor: pointer;']
				],
				[
					'format' => 'raw',
					'label' => Yii::t('topology', 'Network'),
					'value' => function($port){
						return $port->getNetwork()->one()->name;
					}
				],
				[
					'format' => 'raw',
					'label' => Yii::t('topology', 'Device'),
					'value' => function($port){
						return $port->getDevice()->one()->name;
					}
				],
				'name',
				'urn',
				[
					'format' => 'raw',
					'label' => Yii::t('topology', 'VLANs'),
					'value' => function ($port){
						$vlans ="";
						$vlansArray = $port->getVlanRanges()->all();
						$len = count($vlansArray);
						$c = 0;
						foreach ($vlansArray as $vlan):
							$c++;
							$vlans .= $vlan->value;
							if($c < $len) $vlans .= ",";
						endforeach;
						return $vlans;
					},
				],
				'max_capacity',
				'min_capacity',
				'granularity',
				
				
	
			),
		]);
		?>
			
		<?php \yii\widgets\Pjax::end(); ?>
	
		<input class="add" type="button" id="add_button<?php echo $dom->id; ?>" value="<?= Yii::t('topology', 'Add Manual'); ?>" />
					
	    </div> 
	</div>
<?php endforeach; ?>

<br></br>
<?php if($domains) echo '<input class="delete" id="delete_button" type="button" value="'.Yii::t('topology', 'Delete Selected').'"/>'; ?>

<div style="display: none">
<?php Dialog::begin([
		'id' => 'dialog',
    	'clientOptions' => [
        	'modal' => true,
        	'autoOpen' => false,
        	'title' => "Ports",
    	],
	]);

	echo '<br></br>';
    echo '<p style="text-align: left; height: 100%; width:100%;" id="message"></p>';
    
	Dialog::end(); 
?>
</div>
