<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\forms;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use meican\base\components\DateUtils;
use meican\topology\models\Network;
use meican\topology\models\Domain;

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
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $dataProvider;
    }
}
