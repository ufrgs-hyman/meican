<h1><?= Yii::t("topology", "Update aggregator"); ?></h1></h1>

<?=
	$this->render('_form', array(
		'aggregator' => $aggregator,
	)); 
?>