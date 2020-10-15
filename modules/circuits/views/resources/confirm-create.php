<?php 
/**
 * @copyright Copyright (c) 2012-2020 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */
 

use yii\helpers\Html;
use yii\helpers\Url;
?>

<h1>Confirmar Criação do Container</h1>

<p>Nome do Container: <b><?= Html::encode($model->container_name) ?></b>
<p>Porta do Container: <b><?= Html::encode($model->container_port) ?></b>
<p><?=Html::encode($url) ?>
</ul>


<div>
    <a href="./create" id="request-container-btn" class="btn btn-primary">Instanciar novo container</a>    
</div><br>

<?php

