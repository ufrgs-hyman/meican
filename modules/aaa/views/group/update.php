<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

\meican\aaa\assets\group\CreateEdit::register($this);

$this->params['box-title'] = Yii::t('aaa', 'Edit Group');

?>

<?= $this->render('_form', array('group' => $group, 'apps' => $apps,  'root' => $root, 'childsChecked' => $childsChecked)); ?>