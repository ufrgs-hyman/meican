<h1><?= Yii::t("topology", "Synchronizer").' - '.Yii::t("topology", "Update instance"); ?></h1>

<?=
    $this->render('_form', array(
        'model' => $model,
    )); 
?>