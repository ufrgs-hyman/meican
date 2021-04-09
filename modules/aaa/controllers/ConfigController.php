<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\controllers;

use Yii;

use meican\aaa\RbacController;
use meican\aaa\forms\ConfigurationForm;
use meican\aaa\models\AaaPreference;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ConfigController extends RbacController {
    
    public function actionIndex() {
        if(!self::can('group/update')){
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to access Users Configuration'));
            return $this->goHome();
        }
            
        $config = new ConfigurationForm;
        $config->setPreferences(AaaPreference::findAll());

        if ($config->load($_POST)) {
            if(!self::can('configuration/update')){
                Yii::$app->getSession()->addFlash('danger', Yii::t("circuits", "You are not allowed to update the configurations"));
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

        return $this->render('index', array(
                'model' => $config,
        ));
    }
}
