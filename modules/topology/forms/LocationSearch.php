<?php
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\forms;

use Yii;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use meican\base\utils\DateUtils;
use meican\topology\models\Domain;
use meican\topology\models\Network;
use meican\topology\models\Port;
use meican\topology\models\Location;

/**
* @author Leonardo Lauryel Batista dos Santos <@leonardolauryel>
*/
class LocationSearch extends Location {

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
            
            $locations = Location::find()->where(['domain_id' => $domain->id]); 

        } else {
            foreach ($domains as $domain) {
                $validDomains[] = $domain->id;
            }

            $locations = Location::find()->where(['in', 'domain_id', $validDomains]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $locations,
            'sort' => ['defaultOrder' => ['name'=>SORT_ASC]],
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $dataProvider;
    }

    public function searchDomainsWithLocation($allowedDomains){
        $domainIds = [];
        $ids = Location::find()->select('domain_id')->distinct(true)->asArray()->all();

        foreach ($ids as $id) {
            $domainIds[] = $id['domain_id'];
        }

        $domains = Domain::find()->where(['in', 'id', $domainIds])->andWhere(['in', 'id', $allowedDomains])->asArray()->all();
    
        return $domains;
    }

}
