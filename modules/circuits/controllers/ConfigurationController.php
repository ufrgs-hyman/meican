<?php

namespace app\modules\circuits\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use app\modules\circuits\models\ConfigurationForm;
use app\modules\circuits\models\CircuitsPreference;
use Yii;

class ConfigurationController extends RbacController {
    
    public function actionIndex() {
    	if(!self::can('configuration/read')){
    		return $this->goHome();
    	}
    		
        $config = new ConfigurationForm;
        $config->setPreferences(CircuitsPreference::findAll());

        if ($config->load($_POST)) {
        	if(!self::can('configuration/update')){
        		Yii::$app->getSession()->addFlash("warning", Yii::t("circuits", "You don`t allowed to update the configurations"));
        		return $this->render('config', array(
	                'model' => $config,
	        	));
        	}
            if($config->validate() && $config->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("circuits", "Configurations saved successfully"));
            } else {
                foreach($config->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
                $config->clearErrors();
            }
        }

        return $this->render('config', array(
                'model' => $config,
        ));
    }
}
