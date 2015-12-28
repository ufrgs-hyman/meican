<?php

namespace meican\circuits\controllers;

use Yii;

use meican\aaa\controllers\RbacController;
use meican\circuits\forms\ConfigurationForm;
use meican\circuits\models\CircuitsPreference;

class ConfigurationController extends RbacController {
    
    public function actionIndex() {
    	if(!self::can('configuration/read')){
    		return $this->goHome();
    	}
    		
        $config = new ConfigurationForm;
        $config->setPreferences(CircuitsPreference::findAll());

        if ($config->load($_POST)) {
        	if(!self::can('configuration/update')){
        		Yii::$app->getSession()->addFlash("warning", Yii::t("circuits", "You are not allowed to update the configurations"));
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
