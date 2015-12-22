<?php

namespace meican\modules\bpm\models;

use Yii;
use meican\models\Domain;
use yii\data\ActiveDataProvider;
use yii\base\Model;

use meican\models\BpmWorkflow;

/**
 */
class WorkflowSearch extends BpmWorkflow{

	public $domain;
	public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain', 'status'], 'safe'],
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

    public function searchByDomains($params, $allowed_domains){
    	$this->load($params);

    	$domains_name = [];
    	foreach($allowed_domains as $domain) $domains_name[] = $domain->name;
    	
    	if($this->status && $this->domain){
    		if($this->status == "enabled") $workflows = BpmWorkflow::find()->where(['domain' => $this->domain])->andWhere(['in', 'domain', $domains_name])->andWhere(['active' => BpmWorkflow::STATUS_ENABLED])->orderBy(['domain' => SORT_ASC]);
    		else $workflows = BpmWorkflow::find()->where(['domain' => $this->domain])->andWhere(['in', 'domain', $domains_name])->andWhere(['active' => BpmWorkflow::STATUS_DISABLED])->orderBy(['domain' => SORT_ASC]);
    	}
    	else if($this->domain){
    		$workflows = BpmWorkflow::find()->where(['domain' => $this->domain])->andWhere(['in', 'domain', $domains_name])->orderBy(['domain' => SORT_ASC]);
    	}
    	else if($this->status){
    		if($this->status == "enabled") $workflows = BpmWorkflow::find()->where(['in', 'domain', $domains_name])->andWhere(['in', 'domain', $domains_name])->andWhere(['active' => BpmWorkflow::STATUS_ENABLED])->orderBy(['domain' => SORT_ASC]);
    		else $workflows = BpmWorkflow::find()->where(['in', 'domain', $domains_name])->andWhere(['in', 'domain', $domains_name])->andWhere(['active' => BpmWorkflow::STATUS_DISABLED])->orderBy(['domain' => SORT_ASC]);
    	}
    	else {
    		$workflows = BpmWorkflow::find()->where(['in', 'domain', $domains_name])->orderBy(['domain' => SORT_ASC]);
    	}
    	

    	$dataProvider = new ActiveDataProvider([
    			'query' => $workflows,
    			'sort' => false,
    			'pagination' => [
    					'pageSize' => 15,
    			],
    	]);
    	 
    	return $dataProvider;
    }
    
}