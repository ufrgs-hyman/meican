<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->params['header'] = [$name];

?>

<div class="site-error">

    <div class="alert alert-danger">
        Error processing your request. Sorry. :(
    </div>

    <p>
        A error occurred while the Web server was processing your request.
    </p>
    <p>
        This error has been registered. Please use the feedback option in the top menu if you want to report more informations. 

        Thank you.
        </p><p>

        Administrator</p>        
    </p>

</div>
