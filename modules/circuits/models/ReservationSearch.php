<?php

namespace app\modules\circuits\models;

use Yii;
use app\components\DateUtils;
use yii\data\ActiveDataProvider;
use app\models\Reservation;
use app\models\ConnectionPath;
use app\models\Connection;
use yii\base\Model;

/**
 */
class ReservationSearch extends Reservation {

    public $src_domain;
    public $dst_domain;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['src_domain', 'dst_domain'], 'safe'],
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

    public function searchActiveByDomains($params, $domains) {
        $validDomains = [];
        $this->load($params);

        if ($this->src_domain && $this->dst_domain) {
            $dstPaths = ConnectionPath::findBySql("
                SELECT cp1.conn_id
                FROM (
                    SELECT conn_id, MAX(`path_order`) AS last_path
                    FROM `meican_connection_path`     
                    GROUP BY `conn_id`
                    ) cp2
                JOIN    `meican_connection_path` cp1
                ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")->all();

            $allowedConns = [];
            foreach ($dstPaths as $dstPath) {
                $allowedConns[] = $dstPath->conn_id;
            }

            //filtra por conn aceitas pela query anterior
            $connPaths = ConnectionPath::find()->where(['in', 'domain', $this->src_domain])->andWhere(['path_order'=>0])->andWhere(
                ['in', 'conn_id', $allowedConns])->select(
                ["conn_id"])->distinct(true)->all();

        } elseif ($this->src_domain) {
            $connPaths = ConnectionPath::find()->where(['in', 'domain', $this->src_domain])->andWhere(['path_order'=>0])->select(
                ["conn_id"])->distinct(true)->all();

        } elseif ($this->dst_domain) {
            $connPaths = ConnectionPath::findBySql("
                SELECT cp1.conn_id
                FROM (
                    SELECT conn_id, MAX(`path_order`) AS last_path
                    FROM `meican_connection_path`     
                    GROUP BY `conn_id`
                    ) cp2
                JOIN    `meican_connection_path` cp1
                ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")->all();

        } else {
            foreach ($domains as $domain) {
                $validDomains[] = $domain->name;
            }
            $connPaths = ConnectionPath::find()->where(['in', 'domain', $validDomains])->select(["conn_id"])->distinct(true)->all();
        }

        $validConnPaths = [];
        foreach ($connPaths as $connPath) {
            $validConnPaths[] = $connPath->conn_id;
        }

        $validConnections = Connection::find()->where(['>=','finish', DateUtils::now()])->andWhere(['in', 'id', $validConnPaths])->andWhere(
            ['status'=>["PENDING","CREATED","CONFIRMED","SUBMITTED","PROVISIONED"]])->select(["reservation_id"])->distinct(true)->all();

        $validIds = [];
        foreach ($validConnections as $conn) {
            $validIds[] = $conn->reservation_id;
        }
        Yii::trace($validIds);

        $query = self::find()->where(['in', 'id', $validIds])->andWhere(
                        ['type'=>self::TYPE_NORMAL])->orderBy(['date'=>SORT_DESC]);

        // load the search form data and validate

        // adjust the query by adding the filters
        //$query->andFilterWhere(['like', 'name', $this->name]);
        //$query->andFilterWhere(['item_type' => $this->item_type]);
        //$query->andFilterWhere(['type' => $this->type]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

        return $dataProvider;
    }

    public function searchTerminatedByDomains($params, $domains) {
        $validDomains = [];
        $this->load($params);

        if ($this->src_domain && $this->dst_domain) {
            $dstPaths = ConnectionPath::findBySql("
                SELECT cp1.conn_id
                FROM (
                    SELECT conn_id, MAX(`path_order`) AS last_path
                    FROM `meican_connection_path`     
                    GROUP BY `conn_id`
                    ) cp2
                JOIN    `meican_connection_path` cp1
                ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")->all();

            $allowedConns = [];
            foreach ($dstPaths as $dstPath) {
                $allowedConns[] = $dstPath->conn_id;
            }

            //filtra por conn aceitas pela query anterior
            $connPaths = ConnectionPath::find()->where(['in', 'domain', $this->src_domain])->andWhere(['path_order'=>0])->andWhere(
                ['in', 'conn_id', $allowedConns])->select(
                ["conn_id"])->distinct(true)->all();

        } elseif ($this->src_domain) {
            $connPaths = ConnectionPath::find()->where(['in', 'domain', $this->src_domain])->andWhere(['path_order'=>0])->select(
                ["conn_id"])->distinct(true)->all();

        } elseif ($this->dst_domain) {
            $connPaths = ConnectionPath::findBySql("
                SELECT cp1.conn_id
                FROM (
                    SELECT conn_id, MAX(`path_order`) AS last_path
                    FROM `meican_connection_path`     
                    GROUP BY `conn_id`
                    ) cp2
                JOIN    `meican_connection_path` cp1
                ON      cp1.conn_id = cp2.conn_id AND cp1.domain = '".$this->dst_domain."' AND cp1.path_order = cp2.last_path")->all();

        } else {
            foreach ($domains as $domain) {
                $validDomains[] = $domain->name;
            }
            $connPaths = ConnectionPath::find()->where(['in', 'domain', $validDomains])->select(["conn_id"])->distinct(true)->all();
        }

        $validConnIds = [];
        foreach ($connPaths as $connPath) {
            $validConnIds[] = $connPath->conn_id;
        }

        $validConns = Connection::find()->where(['in','id',$validConnIds])->select('reservation_id')->distinct(true)->all();

        $validIds = [];
        foreach ($validConns as $conn) {
           $validIds[] = $conn->reservation_id;
        }

        $invalidConnections = Connection::find()->where(['>=','finish', DateUtils::now()])->andWhere(['status'=>[
                "PENDING","CREATED","CONFIRMED","SUBMITTED","PROVISIONED"]])->select(["reservation_id"])->distinct(true)->all();

        $invalidIds = [];
        foreach ($invalidConnections as $conn) {
           $invalidIds[] = $conn->reservation_id;
        }

        $query = self::find()->where(['not in', 'id', $invalidIds])->andWhere(['in', 'id', $validIds])->andWhere(
                        ['type'=>self::TYPE_NORMAL])->orderBy(['date'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

        return $dataProvider;
    }
}
