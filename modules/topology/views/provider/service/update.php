<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */
?>

<h1><?= Yii::t("topology", "Update service"); ?></h1>

<?=
    $this->render('_form', array(
        'model' => $model,
    )); 
?>