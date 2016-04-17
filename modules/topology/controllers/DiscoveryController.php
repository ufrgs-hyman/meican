<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use meican\aaa\RbacController;
use meican\topology\models\DiscoveryRule;
use meican\topology\models\DiscoveryTask;
use meican\topology\forms\DiscoveryRuleForm;
use meican\topology\models\Change;
use meican\topology\services\DiscoveryService;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryController extends RbacController {

    public function actionIndex() {
        $changeProvider = new ActiveDataProvider([
            'query' => Change::find()->groupBy(['domain'])->select(['*,COUNT(*) AS count']),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $taskProvider = new ActiveDataProvider([
            'query' => DiscoveryTask::find(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $ruleProvider = new ActiveDataProvider([
            'query' => DiscoveryRule::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        return $this->render('index', array(
            'changeProvider' => $changeProvider,
            'ruleProvider' => $ruleProvider,
            'taskProvider' => $taskProvider,
        ));
    }

    public function actionDiscover($id) { 
        $ds = new DiscoveryService;
        $ds->execute($id);
        
        $this->redirect("index");
    }

    public function actionTask($id) {
        $model = DiscoveryTask::findOne($id);

        $searchChange = new Change;
        $changeProvider = $searchChange->searchPending(Yii::$app->request->get(), $id);

        return $this->render('task',[
            'changeProvider' => $changeProvider,
            'model' => $model,
            'searchChange' => $searchChange,
        ]);
    }

    public function actionCreateRule(){
        $form = new DiscoveryRuleForm;
        
        if($form->load($_POST)) {
            if ($form->save()) {
                Yii::$app->getSession()->addFlash("success", 
                    Yii::t("topology", "Rule {name} added successfully", ['name'=>$form->name]));
                return $this->redirect(array('index'));
            } else {
                foreach($form->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
            }
        }
        
        return $this->render('rule/create',[
                'model' => $form,
        ]);
    }
}
