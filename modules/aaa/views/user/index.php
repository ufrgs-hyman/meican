<?php 

use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use app\models\UserDomainRole;

use app\components\LinkColumn;

use yii\helpers\ArrayHelper;

use app\modules\aaa\assets\UserAsset;

UserAsset::register($this);

?>

<h1><?= Yii::t("aaa", 'Users'); ?></h1>

<?php
	$form = ActiveForm::begin([
			'method' => 'post',
			'action' => ['delete'],
			'id' => 'user-form',
			'enableClientScript'=>false,
			'enableClientValidation' => false,
	]);
	
 	echo $this->render('//formButtons');
?>
 	
<?=
		GridView::widget([
		'options' => ['class' => 'list'],
		'dataProvider' => $users,
		'filterModel' => $searchModel,
		'layout' => "{items}{summary}{pager}",
		'columns' => array(
			[
				'class'=> CheckboxColumn::className(),
				'name'=>'delete',
				'checkboxOptions'=> [
					'class'=>'deleteCheckbox',
				],
				'multiple'=>false,
				'headerOptions'=>['style'=>'width: 2%;'],
			],
			[
				'class'=> LinkColumn::className(),
				'image'=>'/images/edit_1.png',
				'url' => 'update',
				'headerOptions'=>['style'=>'width: 2%;'],
			],
			[
				'class'=> LinkColumn::className(),
				'image'=>'/images/role.png',
				'title' => Yii::t('aaa', 'Manage Access Roles'),
				'url' => 'role/index',
				'headerOptions'=>['style'=>'width: 2%;'],
			],
			[
				'label' => Yii::t('aaa', 'User'),
				'value' => 'login',
				'headerOptions'=>['style'=>'width: 39%;'],
			],
			[
				'label' => Yii::t('aaa', 'Name'),
				'value' => 'name',
				'headerOptions'=>['style'=>'width: 39%;'],
			],
			[
				'label' => Yii::t('aaa', '#Roles in Domain'),
				'value' => 'numRoles',
				'filter' => Html::activeDropDownList($searchModel, 'domain',
					ArrayHelper::map($domains, 'name', 'name'),
					['id'=>'dropdown', 'class'=>'form-control','prompt' => Yii::t("bpm", 'any')]),
				'headerOptions'=>['style'=>'width: 16%;'],
			],
			),
		]);
		
	?>

<?php
	ActiveForm::end();
?>