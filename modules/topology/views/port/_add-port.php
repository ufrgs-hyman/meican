<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

echo $this->render('_form', array(
    'formId' => 'add-port-form',
    'networks' => $networks,
	'devices' => $devices,
	'port' => $port,
)); ?>