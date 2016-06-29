<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

use meican\aaa\RbacController;
use meican\topology\models\DiscoveryRule;
use meican\topology\models\DiscoveryTask;
use meican\topology\forms\DiscoveryRuleForm;
use meican\topology\models\Change;
use meican\topology\services\DiscoveryService;

/**
 * @author Maurício Quatrin Guerreiro
 */
class DiscoveryController extends RbacController {

    public function actionIndex() {
        //deve ser feito um switch futuramente para carregamentos do pjax.
        $count = Yii::$app->db->createCommand('
            SELECT COUNT(*) FROM (SELECT * 
                FROM (SELECT * FROM `meican_topo_change` ORDER BY `applied_at` DESC) as t1 
                GROUP BY `domain`) as t2
        ')->queryScalar();

        $changeProvider = new SqlDataProvider([
            'sql' => 'SELECT * 
                FROM (SELECT * 
                    FROM `meican_topo_change` 
                    ORDER BY `applied_at` DESC) as t1 
                GROUP BY `domain`
                ORDER BY `applied_at` DESC',
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 18,
            ],
            'sort' => false
        ]);

        $taskProvider = new ActiveDataProvider([
            'query' => DiscoveryTask::find()->orderBy('id DESC'),
            'sort' => false,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $ruleProvider = new ActiveDataProvider([
            'query' => DiscoveryRule::find(),
            'sort' => false,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        $changeProvider->pagination->pageParam = 'change-page';
        $ruleProvider->pagination->pageParam = 'rule-page';
        $taskProvider->pagination->pageParam = 'task-page';
        
        return $this->render('index', array(
            'changeProvider' => $changeProvider,
            'ruleProvider' => $ruleProvider,
            'taskProvider' => $taskProvider,
        ));
    }

    public function actionExecute($rule) { 
        $ds = new DiscoveryService;
        return $ds->execute(new DiscoveryTask, DiscoveryRule::findOne($rule));
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

    public function actionCreateRule() {
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

    public function actionUpdateRule($id) {
        $form = DiscoveryRuleForm::findOne($id);

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
        
        return $this->render('rule/update',[
                'model' => $form,
        ]);
    }

    public function actionDeleteRule() {
        if(true){
            if(isset($_POST['delete'])){
                foreach ($_POST['delete'] as $id) {
                    $rule = DiscoveryRule::findOne($id);
                    if ($rule->delete()) {
                        Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Rule {name} deleted', ['name'=>$rule->name]));
                    } else {
                        Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting rule {name}', ['name'=>$rule->name]));
                    }
                }
            }
        }
        else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to delete rules'));
    
        return $this->redirect('index');
    }
}
