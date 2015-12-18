<?php

namespace app\modules\topology\models;

use Yii;
use yii\helpers\Html;
use app\models\Notification;
use app\components\DateUtils;
use app\models\TopologyChange;

class TopologyNotification {   

    static function create($user_id, $msg = "", $date = null){
        $not = new Notification();
        $not->user_id = $user_id;
        //Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
        if($date) $not->date = $date;
        else $not->date = DateUtils::now();
        $not->type = Notification::TYPE_TOPOLOGY;
        $not->viewed = 0;
        $not->info = json_encode($msg);
        $not->save();
    }
    
    static function makeHtml($notification = null){
        if($notification == null) return "";
        $eventId = json_decode($notification->info);

        $changesSize = TopologyChange::find()
            ->where(['sync_event_id'=>$eventId])->count();

        $pendingChangesSize = TopologyChange::find()
            ->where(['sync_event_id'=>$eventId, 'status'=>TopologyChange::STATUS_PENDING])->count();

        $appliedChangesSize = TopologyChange::find()
            ->where(['sync_event_id'=>$eventId, 'status'=>TopologyChange::STATUS_APPLIED])->count();

        $changes = TopologyChange::find()
            ->where(['sync_event_id'=>$eventId])->asArray()->groupBy(['domain'])->select(['domain'])->all();

        $title = Yii::t("notification", 'Topology change');
        if (count($changes) > 1) {
            $msg = Yii::t("notification", 'The topologies of')." <b>".count($changes)."</b> ".Yii::t("notification", 'domains has been updated.')." <b>".
            $appliedChangesSize."</b> ".Yii::t("notification", 'changes were applied.').' '.($pendingChangesSize > 0 ? " <b>".$pendingChangesSize."</b> ".Yii::t("notification", 'are pending.') : '');
        } else if (count($changes) == 1){
            $msg = Yii::t("notification", 'The')." <b>".$changes[0]['domain']."</b> ".Yii::t("notification", 'topology has been updated.')." <b>".
            $appliedChangesSize."</b> ".Yii::t("notification", 'changes were applied.').' '.($pendingChangesSize > 0 ? " <b>".$pendingChangesSize."</b> ".Yii::t("notification", 'are pending.') : '');
        } else return "";
        
        $date = Yii::$app->formatter->asDatetime($notification->date);
    
        $link = '/topology/change/applied?eventId='.$eventId;
    
        $text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    
        $html = Notification::makeHtml('topology.png', $text);
         
        if($notification->viewed == true) return '<li>'.Html::a($html, array($link)).'</li>';
        return '<li class="new">'.Html::a($html, array($link)).'</li>';
    }
}