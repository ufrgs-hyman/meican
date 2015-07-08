<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use yii\data\ActiveDataProvider;

use app\models\Device;
use app\models\Network;
use app\models\Domain;
use app\models\Port;
use Yii;
use app\modules\topology\models\DomainForm;

use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\grid\ActionColumn;
use app\components\LinkColumn;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\data\ArrayDataProvider;
use yii\i18n\Formatter;

use yii\helpers\Json;

class PortController extends RbacController {
    
    public function actionGetByDevice($id, $type, $cols=null){
        $query = Port::find()->where(['device_id'=>$id, 'type'=>$type, 'directionality'=> 'BI'])->asArray();
        
        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
    
    public function actionGet($id, $cols=null) {
        $cols ? $urn = Port::find()->where(
                ['id'=>$id])->select($col)->asArray()->one() : $urn = Port::find()->where(['id'=>$id])->asArray()->one();
    
        $temp = Json::encode($urn);
        Yii::trace($temp);
        return $temp;
    }
    
    public function actionGetVlanRanges($id){
        $port = Port::findOne($id);
        $data = $port->getInboundPortVlanRanges()->asArray()->all();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}
