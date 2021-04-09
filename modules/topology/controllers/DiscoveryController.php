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
        if(!self::can('synchronizer/read') && !self::can('domainTopology/read')) {
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to access Topology Discovery'));
            return $this->goHome();
        }
        
        //deve ser feito um switch futuramente para carregamentos do pjax.
        $count = Yii::$app->db->createCommand('
            SELECT count(*) FROM (SELECT domain, max(applied_at)
                FROM meican_topo_change
                group by domain) as t1
        ')->queryScalar();

        $changeProvider = new SqlDataProvider([
            'sql' => 'SELECT domain, max(applied_at) as applied_at
                FROM meican_topo_change
                group by domain',
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
        if(!self::can('synchronizer/read') && !self::can('domainTopology/read')) {
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to perform Topology Discovery'));
            return $this->goHome();
        }

        self::beginAsyncAction();
        
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
        if(!self::can('synchronizer/create')){	
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to create rules on Topology Discovery'));
            return $this->goHome();
        }
        $form = new DiscoveryRuleForm;
        
        if($form->load($_POST)) {
            if ($form->save()) {
                Yii::$app->getSession()->addFlash("success", 
                    Yii::t("topology", "Rule {name} added successfully", ['name'=>$form->name]));
                return $this->redirect(array('index'));
            } 
        }
        
        return $this->render('rule/create',[
                'model' => $form,
        ]);
    }

    public function actionUpdateRule($id) {
        if(!self::can('synchronizer/update')) {
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to update rules on Topology Discovery'));
            return $this->goHome();
        }
        $form = DiscoveryRuleForm::loadFromDB($id);

        if($form->load($_POST)) {
            if ($form->save()) {
                Yii::$app->getSession()->addFlash("success", 
                    Yii::t("topology", "Rule {name} added successfully", ['name'=>$form->name]));
                return $this->redirect(array('index'));
            } 
        }
        
        return $this->render('rule/update',[
                'model' => $form,
        ]);
    }

    public function actionDeleteRule() {
        if(!self::can('synchronizer/delete')) {
            Yii::$app->getSession()->addFlash('danger', Yii::t('topology', 'You are not allowed to delete discovery rules'));
            return $this->goHome();
        }
   
        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $id) {
                $rule = DiscoveryRule::findOne($id);
                if ($rule->delete())
                    Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Rule {name} deleted', ['name'=>$rule->name]));
                else
                    Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting rule {name}', ['name'=>$rule->name]));
            }
        }
        return $this->redirect('index');
    }
}
