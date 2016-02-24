<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\notification\models;

use Yii;
use yii\helpers\Html;

use meican\base\components\DateUtils;
use meican\topology\models\TopologyNotification;
use meican\topology\models\Domain;
use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\Connection;
use meican\aaa\models\User;
use meican\aaa\models\Group;
use meican\aaa\models\AaaNotification;
use meican\circuits\models\AuthorizationNotification;
use meican\circuits\models\ReservationNotification;

/**
 * This is the model class for table "meican_notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $date
 * @property string $type
 * @property integer $viewed
 * @property string $info
 *
 * @property User $id0
 */
class Notification extends \yii\db\ActiveRecord
{    
    const TYPE_AUTHORIZATION =      "AUTHORIZATION";
    const TYPE_RESERVATION =        "RESERVATION";
    const TYPE_TOPOLOGY =			"TOPOLOGY";
    const TYPE_NOTICE =				"NOTICE";
    
    const NOTICE_TYPE_ADD_GROUP =	"ADD_GROUP";
    const NOTICE_TYPE_DEL_GROUP =	"DEL_GROUP";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date', 'type', 'viewed', 'info'], 'required'],
            [['user_id', 'viewed'], 'integer'],
            [['date'], 'safe'],
            [['type'], 'string'],
            [['info'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'type' => 'Type',
            'viewed' => 'Viewed',
            'info' => 'Info',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }
    
    /*public static function makeHtml($image, $text){
        return '<table><tr><td class="image">'.Html::img('@web'.'/images/'.$image).'</td><td>'.$text.'</td></tr></table>';
    }*/
    
    public static function makeHtml($img, $date, $title, $msg, $link = null){
    	$not = '<div class="notification_img pull-left">'.Html::img('@web'.'/images/'.$img).'</div><h4 class="notification_title">'.$title.'</h4>'.'<p class="notification_p">'.$msg.'</p>'.'<h4 class="notification_date"><i class="fa fa-calendar notification_date_img"></i>'.$date.'</h4>';
    	if($link) return Html::a($not, array($link));
    	return Html::a($not, null);
    }
    
    //$text = 
    
    public static function getNumberNotifications(){
        $nots = 0;
    
        if(Yii::$app->user->isGuest) return $nots;
    
        $userId = Yii::$app->user->getId();
    
        return Notification::find()->where(['user_id' => $userId, 'viewed' => 0])->count();
    }
    
    public static function getNumberAuthorizations(){
        $auths = 0;
    
        if(Yii::$app->user->isGuest) return $nots;
         
        $userId = Yii::$app->user->getId();
    
        return Notification::find()->where(['user_id' => $userId, 'viewed' => 0, 'type' => self::TYPE_AUTHORIZATION])->count();
    }
    
    /**
     * GET NOTIFICATIONS
     * @param string $dateParam
     * @return string
     * Retorna o html com até 6 notificações, ja formatado para ser exibido.
     * Quando recebe uma data de entrada, a utiliza como limite, e retorna apenas o que vem depois dela
     */
    public static function getNotifications($dateParam){        
        $userId = Yii::$app->user->getId();

        if(!$dateParam) AuthorizationNotification::clearAuthorizations($userId); //Caso seja a primeira solicitação      

        $array = "";
        $max = 0;
        $date=null;
        
        //Le todas reservas anteriores a data limite, ou todas reservas, caso não exista uma data limite
        if($dateParam) $notifications = Notification::find()->where(['user_id' => $userId])->andWhere(['<','date', $_POST['date']])->orderBy(['date' => SORT_DESC])->all();
        else $notifications = Notification::find()->where(['user_id' => $userId])->orderBy(['date' => SORT_DESC])->all();
        
        //Se não contem, gera aviso de que o usuário não possui notificações
        if(count($notifications) == 0){
            $info = [];
            $info['date'] = null;
            $info['array'] = "<li style='text-align: center;'><span style='float: none !important;'><h2>".Yii::t("notification", 'You don`t have notifications yet.')."</h2></span></li>";
            $info['more'] = false;
            return $info;
        }
        
        //Percorre as notificações gerando o HTML
        foreach($notifications as $notification){
            if($max<6){
                $msg = "";
                switch ($notification->type) {
                    case self::TYPE_AUTHORIZATION:
                        $msg = AuthorizationNotification::makeHtml($notification);
                        break;
                    case self::TYPE_RESERVATION:
                        $msg = ReservationNotification::makeHtml($notification);
                        $notification->viewed = true;
                        $notification->save();
                        break;
                    case self::TYPE_NOTICE:
                        $msg = AaaNotification::makeHtml($notification);
                        $notification->viewed = true;
                        $notification->save();
                        break;
                    case self::TYPE_TOPOLOGY:
                        $msg = TopologyNotification::makeHtml($notification);
                        $notification->viewed = true;
                        $notification->save();
                        break;
                }
                if($msg == ""){
                    $notification->delete();
                }
                else {
                    $array .= $msg;
                    $date = $notification->date;
                }
            }
            if($msg != "")$max++;
            if($max == 7){
                break;
            }
        }
        
        $info = [];
        $info['date'] = $date; //Data da ultima notificação retornada, utilizada como limite para ler as proximas em leituras futuras
        $info['array'] = $array; //HTML a ser exibido
        if($max == 7) $info['more'] = true; //Flag para informar ao JS se existem mais notificações
        else $info['more'] = false;

        return $info;
    }

    /**
     * CREATE NOTICE NOTIFICAION
     * @param string $user_id
     * @param string $type
     * @param string $data
     * @param string $data2
     * @param string $date
     * Cria notificação de alguma noticia. Esta notificações não contém link
     * Internamente ela possui um modelo particular, que armazena um JSON com tipo e e dados
     * Para noticias diferentes, criar apenas novos tipos.
     */
    public static function createNoticeNotification($user_id, $type, $data, $data2 = null, $date = null){         
        $notice = [];
        $notice[0] = $type; //Tipo da noticia
        $notice[1] = $data; //Espaço para armaeznar info
        if($data2) $notice[2] = $data2; //Espaço extra opcional para armazenar mais info
        
        $not = new Notification();
        $not->user_id = $user_id;
        //Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
        if($date) $not->date = $date;
        else $not->date = DateUtils::now();
        $not->type = self::TYPE_NOTICE;
        $not->viewed = 0;
        $not->info = json_encode($notice); //Armazena todos dados em um JSON
        $not->save();
    }
    
    /**
     * CREATE TOPOLOGY NOTIFICAION
     * @param string $msg (A tag no caso)
     * @param string $date
     * Cria notificação de mudança na topologia. VERSÃO BETA
     */
    public static function createTopologyNotification($msg, $date = null){
        $users = User::find()->all();
        foreach($users as $user){
            $not = Notification::findOne(['user_id' => $user->id, 'type' => self::TYPE_TOPOLOGY, 'info' => $msg]);
            if($not){
                //Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
                if($date) $not->date = $date;
                else $not->date = DateUtils::now();
                $not->viewed = 0;
                $not->save();
            }
            else{
                $not = new Notification();
                $not->user_id = $user->id;
                //Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
                if($date) $not->date = $date;
                else $not->date = DateUtils::now();
                $not->type = self::TYPE_TOPOLOGY;
                $not->viewed = 0;
                $not->info = $msg;
                $not->save();
            }
        }
    }
}