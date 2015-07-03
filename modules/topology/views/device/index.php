<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	
	use app\components\LinkColumn;
	
	use yii\helpers\Html;
	
	use yii\i18n\Formatter;

	use app\modules\topology\assets\DeviceAsset;
	
	use yii\widgets\ActiveForm;
	use yii\data\ActiveDataProvider;
	use app\models\Device;
	
	DeviceAsset::register($this);
?>

<h1><?= Yii::t('topology', 'Devices'); ?></h1>

<script>
	var selected_network = <?php echo json_encode($selected_network); ?>;
</script>

<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'device-form',	
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	])
?>
	
<?= $this->render('//formButtons'); ?>
	
<?php foreach ($networks as $net): ?>
    <div id="network<?php echo $net->id; ?>">
	
	    <h4><?=Html::img('@web'.'/images/minus.gif', ['id' => "collapseExpand".$net->id]);?>
	    <?php
		    $text = Yii::t('topology', 'Domain')." ";
		    $text .= $net->getDomain()->one()->name;
	     	$text .= " - ".Yii::t('topology', 'Network')." ";
	        $text .= $net->name;
	        
	        echo $text;
	    ?></h4>
	            
	    <div id="collapsable<?php echo $net->id ?>">  
	       
	    <?=
		GridView::widget([
			'options' => ['class' => 'list-without-margin'],
			'dataProvider' => new ActiveDataProvider([
	    		'query' => Device::find()->where(['network_id' => $net->id]),
	    		'sort' => false,
	    		'pagination' => false,
	    	]),
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' => 'gridDevices',
			'layout' => "{items}",
			'columns' => array(
		    	[
		    		'class'=>CheckboxColumn::className(),
				       'name'=>'delete',         
				       'checkboxOptions'=>[
				       	'class'=>'deleteCheckbox',
				       ],
				       'multiple'=> false,
				       'contentOptions'=>['style'=>'width: 15px;'],
			    ],
			    [
			       	'class'=> LinkColumn::className(),
			       	'image'=>'/images/edit_1.png',
			       	'label' => '',
			       	'url' => 'update',
			       	'title'=> Yii::t('topology', 'Update'),
			       	'contentOptions'=>['style'=>'width: 15px;'],
			    ],
				'name',
				'latitude',
				'longitude',
				[
					'format' => 'html',
					'label' => Yii::t('topology', '#EndPoints'),
					'value' => function($dev){
						return Html::a($dev->getUrns()->count(), ['/topology/urn', 'id' => $dev->getNetwork()->one()->domain_id]);
					}
				],
			),
		]);
		?>
		</div> 
	</div>      

<?php endforeach; ?>

<?php ActiveForm::end(); ?>
