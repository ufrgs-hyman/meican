<?php 
/**
 * @copyright Copyright (c) 2012-2020 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */
 

use yii\helpers\Html;
use yii\helpers\Url;

?>

<h1>Create container</h1>


<div>
    <a href="./index" id="dget-containers-btn" class="btn btn-primary">Verificar containers ativos</a>
</div><br>

<?php

// URL on which we have to post data
$url = "http://blacksabbath.inf.ufrgs.br:15443/aggregator/index.php/aggregator/request_container?container_name=$model->container_name&container_port=$model->container_port";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
$response = curl_exec($ch);
echo $response;