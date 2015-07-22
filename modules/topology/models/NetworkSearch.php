<?php

namespace app\modules\topology\models;

use Yii;
use app\components\DateUtils;
use yii\data\ActiveDataProvider;
use app\models\Network;
use app\models\Domain;
use yii\base\Model;

/**
 */
class NetworkSearch extends Network {

    public $domain_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain_name'], 'safe'],
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

    public function searchByDomains($params, $domains) {
        $validDomains = [];
        $this->load($params);

        if ($this->domain_name) {
        	$domain = Domain::findOne(['name' => $this->domain_name]);
        	$networks = Network::find()->where(['domain_id' => $domain->id]);
        } else {
            foreach ($domains as $domain) {
                $validDomains[] = $domain->id;
            }
            $networks = Network::find()->where(['in', 'domain_id', $validDomains]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $networks,
            'sort' => false,
            /*'pagination' => [
                'pageSize' => 20,
            ]*/
        ]);

        return $dataProvider;
    }
}
