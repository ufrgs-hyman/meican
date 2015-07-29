<h1><?= Yii::t("topology", "Add synchronizer instance"); ?></h1>

<?=
    $this->render('_form', array(
        'model' => $model,
    )); 
?>