<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use Yii;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use meican\base\utils\DateUtils;
use meican\circuits\models\Reservation;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\Connection;
use meican\aaa\models\User;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ReservationSearch extends Reservation {

    public $src_domain;
    public $dst_domain;
    public $dataplane_status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['src_domain', 'dst_domain', 'dataplane_status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        // add related fields to searchable attributes
        return parent::attributes();
    }

    public function searchByDomains($params, $allowedDomains){
        $this->load($params);

        $dataplane_status = ($this->dataplane_status) ? 'ACTIVE' : null;
        $active_status = $dataplane_status ? ['CONFIRMED', 'SUBMITTED', 'PROVISIONED'] : [];
        
        $validDomains = [];
        foreach($allowedDomains as $domain) $validDomains[] = $domain['name'];

        if ($this->src_domain && $this->dst_domain) {
            //foi usado um SQL direto no UNION ao inves do findBySQL pois este
            //apresentou um bug que selecionava todas as colunas
            $connPoints = ConnectionPath::find()
                ->where(['in', 'domain', [$this->src_domain]])
                ->andWhere(['path_order'=>0])
                ->select(["conn_id"])
                ->distinct(true)
                ->union("
                SELECT cp1.conn_id as conn_id
                FROM (
                    SELECT conn_id, MAX(`path_order`) AS last_path
                    FROM `meican_connection_path`
                    GROUP BY `conn_id`
                    ) cp2
                JOIN    `meican_connection_path` cp1
                ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path");

        } elseif ($this->src_domain) {
            $connPoints = ConnectionPath::find()
                ->where(['in', 'domain', [$this->src_domain]])
                ->andWhere(['path_order'=>0])
                ->select(["conn_id"])
                ->distinct(true);

        } elseif ($this->dst_domain) {
            $connPoints = ConnectionPath::findBySql("
                SELECT DISTINCT cp1.conn_id as conn_id
                FROM (
                    SELECT conn_id, MAX(`path_order`) AS last_path
                    FROM `meican_connection_path`
                    GROUP BY `conn_id`
                    ) cp2
                JOIN    `meican_connection_path` cp1
                ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path");
        } else {
            $connPoints = ConnectionPath::find()            //$connPoints tem uma tabela somente com as linhas que contem domínios que o usuário tem permissão
                ->where(['in', 'domain', $validDomains])
                ->select(["conn_id"])
                ->distinct(true);
        }
        
        $validConns = Connection::find()                    //$validConns tem uma tabela com as linhas que tem o "id" igual a "conn_id"(da tabela $connPoints ) 
            ->where(['in', 'id', 
                ArrayHelper::getColumn(
                    $connPoints->all(),
                    'conn_id')]);
     
        $userOwned = Reservation::find()                    //$userOwned seleciona os ID's de reservas criadas pelo usuário atual
            ->where(['in', 'request_user_id', [Yii::$app->user->getId()]])
            ->select(["id"])
            ->distinct(true);
                    
        $reservations = Reservation::find()                 //$reservations tem uma tabela com as linhas que tem o "id" igual aos do "reservation_id"(da tabela $validConns)
            ->andWhere(['in', 'id', 
                ArrayHelper::getColumn(
                    $validConns->select(['reservation_id'])->distinct(true)->asArray()->all(),
                    'reservation_id')])
            ->orWhere(['in', 'id', 
                ArrayHelper::getColumn(
                    $userOwned->select(['id'])->distinct(true)->asArray()->all(),
                    'id')])
            ->andWhere(['type'=>self::TYPE_NORMAL]);
            
        $reservationHelper = ArrayHelper::getColumn($reservations->all(),'id');
       
        $currentDate = date("o-m-d H:i:s");

        if($dataplane_status)   {
            $connsPast = Connection::find()
               ->andwhere(['in', 'reservation_id', $reservationHelper])
                ->andWhere(['<', 'finish', $currentDate])
                ->andWhere(['dataplane_status' => 'ACTIVE'])
                ->andWhere(['in', 'status', $active_status])    
                ->orderBy(['start'=>SORT_DESC]);

            $connsCurrent = Connection::find()
                ->andwhere(['in', 'reservation_id', $reservationHelper])
                ->andWhere(['<', 'start', $currentDate])
                ->andWhere(['>', 'finish', $currentDate])
                ->andWhere(['dataplane_status' => 'ACTIVE'])
                ->andWhere(['in', 'status', $active_status])
                ->orderBy(['start'=>SORT_DESC]);

            $connsFuture = Connection::find()
                ->andwhere(['in', 'reservation_id', $reservationHelper])
                ->andWhere(['>', 'start', $currentDate])
                ->andWhere(['dataplane_status' => 'ACTIVE'])
                ->andWhere(['in', 'status', $active_status])    
                ->orderBy(['start'=>SORT_DESC]);
        } else  {
            $connsPast = Connection::find()
                ->andwhere(['in', 'reservation_id', $reservationHelper])
                ->andWhere(['<', 'finish', $currentDate])
                ->orderBy(['start'=>SORT_DESC]);

            $connsCurrent = Connection::find()
                ->andwhere(['in', 'reservation_id', $reservationHelper])
                ->andWhere(['<', 'start', $currentDate])
                ->andWhere(['>', 'finish', $currentDate])
                ->orderBy(['start'=>SORT_DESC]);

            $connsFuture = Connection::find()
                ->andwhere(['in', 'reservation_id', $reservationHelper])
                ->andWhere(['>', 'start', $currentDate])
                ->orderBy(['start'=>SORT_DESC]);
        }

        $past = new ActiveDataProvider([
            'query' => $connsPast,
            'sort' =>[
                'attributes' => [
                    'start' => [
                        'asc' => ['start' => SORT_ASC],
                        'desc' => ['start' => SORT_DESC],
                    ],
                    'finish' => [
                        'asc' => ['finish' => SORT_ASC],
                        'desc' => ['finish' => SORT_DESC],
                    ],
                    'bandwidth' => [
                        'asc' => ['bandwidth' => SORT_ASC],
                        'desc' => ['bandwidth' => SORT_DESC],
                    ],

                ],
            ],
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        $current = new ActiveDataProvider([
            'query' => $connsCurrent,
            'sort' =>[
                'attributes' => [
                    'start' => [
                        'asc' => ['start' => SORT_ASC],
                        'desc' => ['start' => SORT_DESC],
                    ],
                    'finish' => [
                        'asc' => ['finish' => SORT_ASC],
                        'desc' => ['finish' => SORT_DESC],
                    ],
                    'bandwidth' => [
                        'asc' => ['bandwidth' => SORT_ASC],
                        'desc' => ['bandwidth' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        $future = new ActiveDataProvider([
            'query' => $connsFuture,
            'sort' =>[
                'attributes' => [
                    'start' => [
                        'asc' => ['start' => SORT_ASC],
                        'desc' => ['start' => SORT_DESC],
                    ],
                    'finish' => [
                        'asc' => ['finish' => SORT_ASC],
                        'desc' => ['finish' => SORT_DESC],
                    ],
                    'bandwidth' => [
                        'asc' => ['bandwidth' => SORT_ASC],
                        'desc' => ['bandwidth' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        $dataProvider = ['past'=>$past, 'current'=>$current, 'future'=>$future];
        
        return $dataProvider;
    }

    public function searchActiveByDomains($params, $allowed_domains){
        $validDomains = [];
        $this->load($params);
        $userId = Yii::$app->user->getId();
        
        $domains_name = [];
        foreach($allowed_domains as $domain) $domains_name[] = $domain->name;
        
        $connPaths = [];
        if ($this->src_domain && $this->dst_domain) {
            //if(in_array($this->src_domain, $domains_name) && in_array($this->dst_domain, $domains_name)){
                $dstPaths = ConnectionPath::findBySql("
                    SELECT cp1.conn_id
                    FROM (
                        SELECT conn_id, MAX(`path_order`) AS last_path
                        FROM `meican_connection_path`
                        GROUP BY `conn_id`
                        ) cp2
                    JOIN    `meican_connection_path` cp1
                    ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")
                    ->asArray()
                    ->all();
                 
                $allowedConns = [];
                foreach ($dstPaths as $dstPath) {
                    $allowedConns[] = $dstPath['conn_id'];
                }
                 
                //filtra por conn aceitas pela query anterior
                $connPaths = ConnectionPath::find()
                    ->where(['in', 'domain', [$this->src_domain]])
                    ->andWhere(['path_order'=>0])
                    ->andWhere(['in', 'conn_id', $allowedConns])
                    ->select(["conn_id"])
                    ->distinct(true)
                    ->asArray()
                    ->all();
            //}
        } elseif ($this->src_domain) {
            //if(in_array($this->src_domain, $domains_name)){
                $connPaths = ConnectionPath::find()
                    ->where(['in', 'domain', [$this->src_domain]])
                    ->andWhere(['path_order'=>0])
                    ->select(["conn_id"])
                    ->distinct(true)
                    ->asArray()
                    ->all();
            //}
        } elseif ($this->dst_domain) {
            //if(in_array($this->dst_domain, $domains_name)){
                $connPaths = ConnectionPath::findBySql("
                    SELECT cp1.conn_id
                    FROM (
                        SELECT conn_id, MAX(`path_order`) AS last_path
                        FROM `meican_connection_path`
                        GROUP BY `conn_id`
                        ) cp2
                    JOIN    `meican_connection_path` cp1
                    ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")
                    ->asArray()
                    ->all();
            //}
        } else {
            foreach ($allowed_domains as $domain) {
                $validDomains[] = $domain->name;
            }
            $connPaths = ConnectionPath::find()
                ->where(['in', 'domain', $validDomains])
                ->select(["conn_id"])
                ->distinct(true)
                ->asArray()
                ->all();
        }
        
        $validConnIds = [];
        foreach ($connPaths as $connPath) {
            $validConnIds[] = $connPath['conn_id'];
        }
        
        $validConnections = Connection::find()
            ->where(['>=','finish', DateUtils::now()])
            ->andWhere(['in', 'id', $validConnIds])
            ->andWhere(['status'=>["PENDING","CREATED","CONFIRMED","SUBMITTED","PROVISIONED"]])
            ->select(["reservation_id"])
            ->distinct(true)
            ->asArray()
            ->all();
        
        $validIds = [];
        foreach ($validConnections as $conn) {
            $validIds[] = $conn['reservation_id'];
        }

        if(!$this->src_domain && !$this->dst_domain){

            //Pega todas reservas validas para casar com as feitas pelo usuário
            $allValidConnections = Connection::find()
                ->where(['>=','finish', DateUtils::now()])
                ->andWhere(['status'=>["PENDING","CREATED","CONFIRMED","SUBMITTED","PROVISIONED"]])
                ->select(["reservation_id"])
                ->distinct(true)
                ->asArray()
                ->all();
            
            $allValidIds = [];
            foreach ($allValidConnections as $conn) {
                $allValidIds[] = $conn['reservation_id'];
            }
            
            $reservationsUser = Reservation::find()
                ->where(['in', 'id', $allValidIds])
                ->andWhere(['request_user_id' => Yii::$app->user->getId()])
                ->andWhere(['type'=>self::TYPE_NORMAL])
                ->orderBy(['date'=>SORT_DESC]);

            $reservations = Reservation::find()
                ->where(['in', 'id', $validIds])
                ->andWhere(['type'=>self::TYPE_NORMAL])
                ->orderBy(['date'=>SORT_DESC]);
            
            $reservations = $reservations->union($reservationsUser);
            $reservations = $reservations->asArray()->all();
            
        } else {
            $reservations = Reservation::find()
                ->andWhere(['in', 'id', $validIds])
                ->andWhere(['type'=>self::TYPE_NORMAL])
                ->orderBy(['date'=>SORT_DESC])
                ->asArray()
                ->all();
        }         

        $validResIds = [];
        foreach($reservations as $res){
            if($res['request_user_id'] == $userId){
                $validResIds[] = $res['id'];
            }
            else if(!$this->request_user) {
                $conns = Connection::find()
                    ->where(['reservation_id' => $res['id']])
                    ->select(["id"])
                    ->asArray()
                    ->all();
                if(!empty($conns)){
                    $conn_ids = [];
                    foreach($conns as $conn) $conn_ids[] = $conn['id'];

                    $paths = ConnectionPath::find()
                        ->where(['in', 'domain', $domains_name])
                        ->andWhere(['in', 'conn_id', $conn_ids])
                        ->select(["conn_id"])
                        ->distinct(true)
                        ->asArray()
                        ->all();
        
                    if(!empty($paths)){
                        $validResIds[] = $res['id'];
                    }
                }
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Reservation::find()
                ->where(['in', 'id', $validResIds])
                ->orderBy(['date'=>SORT_DESC]),
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        
        return $dataProvider;
    }
    
    public function searchTerminatedByDomains($params, $allowed_domains){
        $validDomains = [];
        $this->load($params);
        $userId = Yii::$app->user->getId();
        
        $domains_name = [];
        foreach($allowed_domains as $domain) $domains_name[] = $domain->name;
        
        $connPaths = [];
        if ($this->src_domain && $this->dst_domain) {
            //if(in_array($this->src_domain, $domains_name) && in_array($this->dst_domain, $domains_name)){
                $dstPaths = ConnectionPath::findBySql("
                    SELECT cp1.conn_id
                    FROM (
                        SELECT conn_id, MAX(`path_order`) AS last_path
                        FROM `meican_connection_path`
                        GROUP BY `conn_id`
                        ) cp2
                    JOIN    `meican_connection_path` cp1
                    ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")
                    ->asArray()
                    ->all();
                 
                $allowedConns = [];
                foreach ($dstPaths as $dstPath) {
                    $allowedConns[] = $dstPath['conn_id'];
                }
                 
                //filtra por conn aceitas pela query anterior
                $connPaths = ConnectionPath::find()
                    ->where(['in', 'domain', [$this->src_domain]])
                    ->andWhere(['path_order'=>0])
                    ->andWhere(['in', 'conn_id', $allowedConns])
                    ->select(["conn_id"])
                    ->distinct(true)
                    ->asArray()
                    ->all();
            //}
        } elseif ($this->src_domain) {
            //if(in_array($this->src_domain, $domains_name)){
                $connPaths = ConnectionPath::find()
                    ->where(['in', 'domain', [$this->src_domain]])
                    ->andWhere(['path_order'=>0])
                    ->select(["conn_id"])
                    ->distinct(true)
                    ->asArray()
                    ->all();
            //}
        } elseif ($this->dst_domain) {
            //if(in_array($this->dst_domain, $domains_name)){
                $connPaths = ConnectionPath::findBySql("
                    SELECT cp1.conn_id
                    FROM (
                        SELECT conn_id, MAX(`path_order`) AS last_path
                        FROM `meican_connection_path`
                        GROUP BY `conn_id`
                        ) cp2
                    JOIN    `meican_connection_path` cp1
                    ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")
                    ->asArray()
                    ->all();
            //}
        } else {
            foreach ($allowed_domains as $domain) {
                $validDomains[] = $domain->name;
            }
            $connPaths = ConnectionPath::find()
                ->where(['in', 'domain', $validDomains])
                ->select(["conn_id"])
                ->distinct(true)
                ->asArray()
                ->all();
        }
        
        $validConnIds = [];
        foreach ($connPaths as $connPath) {
            $validConnIds[] = $connPath['conn_id'];
        }

        $validConns = Connection::find()
            ->where(['in','id',$validConnIds])
            ->select('reservation_id')
            ->distinct(true)
            ->asArray()
            ->all();
        
        $validIds = [];
        foreach ($validConns as $conn) {
            $validIds[] = $conn['reservation_id'];
        }
        
        $invalidConnections = Connection::find()
            ->where(['>=','finish', DateUtils::now()])
            ->andWhere(['status'=>["PENDING","CREATED","CONFIRMED","SUBMITTED","PROVISIONED"]])
            ->select(["reservation_id"])
            ->distinct(true)
            ->asArray()
            ->all();
        
        $invalidIds = [];
        foreach ($invalidConnections as $conn) {
            $invalidIds[] = $conn['reservation_id'];
        }
        
        if(!$this->src_domain && !$this->dst_domain){
            $reservations = Reservation::find()
                ->where(['in', 'id', $validIds])
                ->orWhere(['request_user_id' => Yii::$app->user->getId()])
                ->andWhere(['not in', 'id', $invalidIds])
                ->andWhere(['type'=>self::TYPE_NORMAL])
                ->orderBy(['date'=>SORT_DESC])
                ->asArray()
                ->all(); 
        } else {
            $reservations = Reservation::find()
                ->where(['not in', 'id', $invalidIds])
                ->andWhere(['in', 'id', $validIds])
                ->andWhere(['type'=>self::TYPE_NORMAL])
                ->orderBy(['date'=>SORT_DESC])
                ->asArray()
                ->all(); 
        }
        
        $validResIds = [];
        foreach($reservations as $res){
            
            if($res['request_user_id'] == $userId){
                $validResIds[] = $res['id'];
            }
            else if(!$this->request_user) {
                $conns = Connection::find()
                    ->where(['reservation_id' => $res['id']])
                    ->select(["id"])
                    ->asArray()
                    ->all();

                if(!empty($conns)){
                    $conn_ids = [];
                    foreach($conns as $conn) $conn_ids[] = $conn['id'];

                    $paths = ConnectionPath::find()
                        ->where(['in', 'domain', $domains_name])
                        ->andWhere(['in', 'conn_id', $conn_ids])
                        ->select(["conn_id"])
                        ->distinct(true)
                        ->asArray()
                        ->all();
                    
                    if(!empty($paths)){
                        $validResIds[] = $res['id'];
                    }
                }
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Reservation::find()
                ->where(['in', 'id', $validResIds])
                ->orderBy(['date'=>SORT_DESC]),
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
         
        return $dataProvider;
    }
}
