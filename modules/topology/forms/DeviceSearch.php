<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\forms;

use Yii;
use yii\data\ActiveDataProvider;
use yii\base\Model;

use meican\base\utils\DateUtils;
use meican\topology\models\Device;
use meican\topology\models\Domain;

class DeviceSearch extends Device {

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
            
        	Yii::trace($this->domain_name);
        	$domain = Domain::findOne(['name' => $this->domain_name]);
        	
        	$devices = Device::find()->where(['domain_id' => $domain->id]);

        } else {
            foreach ($domains as $domain) {
                $validDomains[] = $domain->id;
            }
            $devices = Device::find()->where(['in', 'domain_id', $validDomains]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $devices,
            'sort' => false,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $dataProvider;
    }
}
