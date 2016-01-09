<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\data\ActiveDataProvider;

use meican\aaa\RbacController;
use meican\topology\models\DiscoveryRule;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryController extends RbacController {

    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
                'query' => DiscoveryRule::find(),
        ]);
        
        return $this->render('index', array(
                'data' => $dataProvider,
        ));
    }
}
