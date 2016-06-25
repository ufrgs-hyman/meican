<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;

$doc = new \DOMDocument('1.0');
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->loadXML($model->message);

echo '<b>'.$model->getTypeLabel()."</b> by ".$model->getAuthor().' at '.
Yii::$app->formatter->asDatetime($model->created_at).
'<br>'.Html::textarea('message', $doc->saveXML(), ['rows'=> 20, 'cols'=> 140, 'readOnly' => true]);

?>