<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\home\controllers;

use Yii;

use meican\base\BaseController;

/**
 * @author Maurício Quatrin Guerreiro
 */
class BoardController extends BaseController {

    public $defaultAction = 'dashboard';
	
	public function actions()
	{
		return [
				'error' => [
						'class' => 'yii\web\ErrorAction',
				],
		];
	}
	
	public function actionDashboard() {
    	return $this->render('dashboard');
	}
}

?>