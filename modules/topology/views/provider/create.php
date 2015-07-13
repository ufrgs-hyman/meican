<h1><?= Yii::t("topology", "Add provider"); ?></h1>

<?=
    $this->render('_form', array(
        'model' => $model,
    )); 
?>