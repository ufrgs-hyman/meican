<?php 
	use yii\jui\Dialog;
	use yii\helpers\Html;
	
	use app\modules\bpm\assets\IndexCreateAsset;
	IndexCreateAsset::register($this);
?>

<?= Html::csrfMetaTags() ?>

<div style="display: none">
<?php Dialog::begin([
		'id' => 'dialog',
		'clientOptions' => [
				'modal' => true,
				'closeOnEscape' => false,
				'autoOpen' => false,
				'title' => Yii::t("bpm", "Select the Domain"),
		],
]);

echo '<label style="width:100%;" for="name" id="MessageLabel">'.Yii::t("bpm", 'Please select the Domain for which you want create a Workflow:').'</label>';
echo '<br></br><select id="selectDomain"></select>';

Dialog::end();
?>
</div>