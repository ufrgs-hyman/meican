<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

echo $this->render('_form-system-role', array(
    'formId' => 'add-role-system-form',
    'udr' => $udr, 
    'groups' => $groups, 
)); ?>