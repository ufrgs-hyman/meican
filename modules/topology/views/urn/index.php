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
	use app\models\Urn;
	use yii\jui\Dialog;
	
	use app\modules\topology\assets\UrnsAsset;
	UrnsAsset::register($this);
?>


<?= Html::csrfMetaTags() ?>

<script>
	var selected_domain = <?php echo json_encode($selected_domain); ?>;
</script>

<h1><?php echo Yii::t('topology', 'URNs (Uniform resource name)'); ?></h1>
	
<?php foreach ($domains as $dom): ?>
    <div id="domain<?php echo $dom->getId(); ?>">

    	<h4><?=Html::img('@web'.'/images/minus.gif', ['id' => "collapseExpand".$dom->getId()]);?>
        <?php
        	$text = Yii::t('topology', 'Domain')." ";
            $text .= ($dom->getTopoId()) ? $dom->getDescr() : $dom->getDescr();
            echo $text;
        ?></h4>
	            
        <div id="collapsable<?php echo $dom->getId() ?>">                
                
        <?php \yii\widgets\Pjax::begin([
		    'id' => 'pjaxContainer'.$dom->getId(),
		]); ?>
	
		<?=
		GridView::widget([
			'options' => ['class' => 'list'],
			'formatter' => new Formatter(['nullDisplay'=>'']),
			'id' =>'grid'.$dom->getId(),
			'emptyText' => Yii::t('topology', 'No URLs added to this domain'),
			'dataProvider' => new ArrayDataProvider([
				'models' => $dom->getUrns(),
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
					'value' => function ($urn){
						return Html::img('@web'.'/images/edit_1.png', ['title' => Yii::t('topology', 'Update'), 'onclick' => "editURN(this, $urn->id)"]);
					},
					'contentOptions'=>['style'=>'width: 15px; cursor: pointer;'],
				],
				[
					'format' => 'raw',
					'value' => function ($urn){
						return Html::img('@web'.'/images/remove.png', ['title' => Yii::t('topology', 'Delete'), 'onclick' => "deleteURN($urn->id)"]);
					},
					'contentOptions'=>['style'=>'width: 15px; cursor: pointer;']
				],
				[
					'label' => Yii::t('topology', 'Network'),
					'value'=> 'network',
					'contentOptions'=>['style'=>'width: 110px; max-width: 110px;'],
				],
				'device',
				'port',
				'value',
				'vlans',
				'max_capacity',
				'min_capacity',
				'granularity',
	
			),
		]);
		?>
			
		<?php \yii\widgets\Pjax::end(); ?>
	
		<input class="add" type="button" id="add_button<?php echo $dom->getId(); ?>" value="<?= Yii::t('topology', 'Add Manual'); ?>" />
					
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
        	'title' => "URNs",
    	],
	]);

	echo '<br></br>';
    echo '<p style="text-align: left; height: 100%; width:100%;" id="message"></p>';
    
	Dialog::end(); 
?>
</div>
