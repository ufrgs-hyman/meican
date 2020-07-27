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
$url = "http://blacksabbath.inf.ufrgs.br:15443/aggregator/index.php/aggregator/request_container";

// Any other field you might want to post
//$json_data = json_encode(array("name"=>"PHP Rockstart", "age"=>29));
//$post_data['json_data'] = $json_data;
//$post_data['secure_hash'] = mktime();

// Initialize cURL
$ch = curl_init();

// Set URL on which you want to post the Form and/or data
curl_setopt($ch, CURLOPT_URL, $url);
// Data+Files to be posted
//curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
// Pass TRUE or 1 if you want to wait for and catch the response against the request made
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// For Debug mode; shows up any error encountered during the operation
//curl_setopt($ch, CURLOPT_VERBOSE, 1);
// Execute the request
$response = curl_exec($ch);

// Just for debug: to see response
echo $response;