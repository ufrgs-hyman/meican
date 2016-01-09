<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\home\controllers;

use Yii;

use meican\base\BaseController;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DefaultController extends BaseController {
	
	public function actions()
	{
		return [
				'error' => [
						'class' => 'yii\web\ErrorAction',
				],
		];
	}
	
	public function actionIndex() {
    	return $this->render('dashboard');
	}
}

?>