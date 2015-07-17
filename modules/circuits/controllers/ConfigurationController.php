<?php

namespace app\modules\circuits\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use app\modules\circuits\models\ConfigurationForm;
use app\modules\circuits\models\CircuitsPreference;
use Yii;

class ConfigurationController extends RbacController {
    
    public function actionIndex() {
        //elf::canRedir('reservation/config');

        $config = new ConfigurationForm;
        $config->setPreferences(CircuitsPreference::findAll());

        if ($config->load($_POST)) {
            if($config->validate() && $config->save()) {
                Yii::$app->getSession()->addFlash("success", "Configurations saved successfully");
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
