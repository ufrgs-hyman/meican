<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\models;

use Yii;
use yii\helpers\Html;

use meican\notify\models\Notification;
use meican\base\utils\DateUtils;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
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

        $changesSize = Change::find()
            ->where(['sync_event_id'=>$eventId])->count();

        $pendingChangesSize = Change::find()
            ->where(['sync_event_id'=>$eventId])->andWhere(['in', 'status', [Change::STATUS_PENDING, Change::STATUS_FAILED]])->count();

        $appliedChangesSize = Change::find()
            ->where(['sync_event_id'=>$eventId, 'status'=>Change::STATUS_APPLIED])->count();

        $changes = Change::find()
            ->where(['sync_event_id'=>$eventId])->asArray()->groupBy(['domain'])->select(['domain'])->all();

        $title = Yii::t("notify", 'Topology change');
        if (count($changes) > 1) {
            $msg = Yii::t("notify", 'The topologies of')." <b>".count($changes)."</b> ".Yii::t("notify", 'domains has been updated.')." <b>".
            $appliedChangesSize."</b> ".Yii::t("notify", 'changes were applied.').' '.($pendingChangesSize > 0 ? " <b>".$pendingChangesSize."</b> ".Yii::t("notify", 'are pending.') : '');
        } else if (count($changes) == 1){
            $msg = Yii::t("notify", 'The')." <b>".$changes[0]['domain']."</b> ".Yii::t("notify", 'topology has been updated.')." <b>".
            $appliedChangesSize."</b> ".Yii::t("notify", 'changes were applied.').' '.($pendingChangesSize > 0 ? " <b>".$pendingChangesSize."</b> ".Yii::t("notify", 'are pending.') : '');
        } else return "";
        
        $date = Yii::$app->formatter->asDatetime($notification->date);
    
        $link = '/topology/change/applied?eventId='.$eventId;
    
        $text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    
        $html = Notification::makeHtml('topology.png', $text);
         
        if($notification->viewed == true) return '<li>'.Html::a($html, array($link)).'</li>';
        return '<li class="new">'.Html::a($html, array($link)).'</li>';
    }
}