<?php 
/**
 * @copyright Copyright (c) 2012-2020 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */
 

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<h1>Aggregator API</h1>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'container_name') ?>

    <?= $form->field($model, 'container_port') ?>

    <div class="form-group">
        <?= Html::submitButton('Criar', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>


<?php

$url = "http://blacksabbath.inf.ufrgs.br:15443/aggregator/index.php/aggregator/get_containers";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);

$response = curl_exec($ch);

echo $response;