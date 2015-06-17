<h1><?= Yii::t("topology", "Add aggregator"); ?></h1>

<?=
	$this->render('_form', array(
		'aggregator' => $aggregator,
	)); 
?>