<h1><?= Yii::t("topology", "Synchronizer").' - '.Yii::t("topology", "Add instance"); ?></h1>

<?=
    $this->render('_form', array(
        'model' => $model,
    )); 
?>