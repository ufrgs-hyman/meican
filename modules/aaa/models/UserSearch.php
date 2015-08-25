<?php

namespace app\modules\aaa\models;

use Yii;
use app\models\User;
use app\modules\aaa\models\UserSearchForm;
use app\models\UserDomainRole;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\base\Model;

use app\models\BpmWorkflow;

/**
 */
class UserSearch extends UserSearchForm{

	public $domain;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain'], 'safe'],
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

    public function searchByDomains($params, $allowed_domains, $root){
    	$this->load($params);
    	
    	Yii::trace($this->domain);

    	$domains_name = [];
    	foreach($allowed_domains as $domain) $domains_name[] = $domain->name;

    	if(!$root){
    		if($this->domain) $users = UserDomainRole::find()->where(['in', 'domain', $this->domain])->all();
    		else $users = UserDomainRole::find()->where(['in', 'domain', $domains_name])->all();
    		$users_id = [];
    		foreach($users as $user){
    			$users_id[] = $user->user_id;
    		}
    	}
    	else{
    		if($this->domain){
    			$users = UserDomainRole::find()->where(['in', 'domain', $this->domain])->all();
    			$users_id = [];
    			foreach($users as $user){
    				$users_id[] = $user->user_id;
    			}
    		}
    		else {
	    		$users = User::find()->all();
	    		$users_id = [];
	    		foreach($users as $user){
	    			$users_id[] = $user->id;
	    		}
    		}
    	}
    	
    	$users = User::find()->where(['in', 'id', $users_id])->all();
    	
    	$userForm = [];
    	foreach($users as $user){
    		$aux = new UserSearchForm();
    		if($this->domain) $count = UserDomainRole::find()->where(['user_id' => $user->id, 'domain' => $this->domain])->count();
    		else{
    			if(!$root) $count = UserDomainRole::find()->where(['user_id' => $user->id])->andWhere(['in', 'domain', $domains_name])->select('DISTINCT `domain`')->count();
    		 	else $count = UserDomainRole::find()->where(['user_id' => $user->id])->select('DISTINCT `domain`')->count();
    			
    		}
    		$aux->setData($user, $count);
    		$userForm[$aux->id] = $aux;
    	}
    	
    	$data = new ArrayDataProvider([
    			'allModels' => $userForm,
    			'sort' => false,
    			'pagination' => [
    					'pageSize' => 15,
    			],
    	]);
    	 
    	return $data;
    }
    
}