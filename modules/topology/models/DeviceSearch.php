<?php

namespace app\modules\topology\models;

use Yii;
use app\components\DateUtils;
use yii\data\ActiveDataProvider;
use app\models\Device;
use app\models\Domain;
use yii\base\Model;

/**
 */
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

    public function searchActiveByDomains($params, $domains) {
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
            /*'pagination' => [
                'pageSize' => 20,
            ]*/
        ]);

        return $dataProvider;
    }

    public function searchTerminatedByDomains($params, $domains) {
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
            /*'pagination' => [
                'pageSize' => 20,
            ]*/
        ]);

        return $dataProvider;

    }
}
