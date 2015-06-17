<?php

namespace app\modules\init\controllers;

use Yii;

use app\controllers\RbacController;

class DefaultController extends RbacController {
	
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