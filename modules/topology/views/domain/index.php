<?php 
	use yii\grid\GridView;
	use yii\grid\CheckboxColumn;
	use yii\widgets\ActiveForm;

	use meican\base\widgets\GridButtons;
	use meican\base\components\LinkColumn;
	use meican\topology\assets\domain\IndexAsset;
	
	IndexAsset::register($this);

$this->params['header'] = ["Domains", ['Home', 'Topology']];

?>

<div class="box box-default">
	<div class="box-header with-border">
	  	<?= GridButtons::widget(); ?>
	</div>
	<div class="box-body">
		<?php
			$form = ActiveForm::begin([
				'method' => 'post',
				'action' => ['delete'],
				'id' => 'domain-form',
				'enableClientScript'=>false,
            	'enableClientValidation' => false,
			]);
			
		?>
		<?=
			GridView::widget([
				'dataProvider' => $domains,
				'id' => 'grid',
				'layout' => "{items}{summary}{pager}",
				'columns' => array(
			    		array(
			    			'class'=>CheckboxColumn::className(),
					        'name'=>'delete',         
					        'multiple'=>false,
					        'headerOptions'=>['style'=>'width: 2%;'],
				        ),
				        array(
				        	'class'=> LinkColumn::className(),
				        	'image'=>'/images/edit_1.png',
				        	'label' => '',
				        	'title'=> Yii::t("topology", 'Update'),
				        	'url' => '/topology/domain/update',
				        	'headerOptions'=>['style'=>'width: 2%;'],
				        ),
						[
							'label' => Yii::t('topology', 'Name'),
				        	'value' => 'name',
							'headerOptions'=>['style'=>'width: 50%;'],
						],
				        [
				        	'label' => Yii::t('topology', 'Default Policy'),
				        	'value' => function($dom){
				        		return $dom->getPolicy();
				        	},
				        	'headerOptions'=>['style'=>'width: 46%;'],
						],
					),
			]);
		?>
		<?php	
			ActiveForm::end();
		?>
	</div>
</div>
