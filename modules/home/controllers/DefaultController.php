<?php

namespace meican\home\controllers;

use Yii;

use meican\base\BaseController;

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