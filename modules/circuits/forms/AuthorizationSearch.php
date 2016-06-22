<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use Yii;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use yii\data\ArrayDataProvider;

use meican\base\utils\DateUtils;
use meican\topology\models\Domain;
use meican\circuits\models\Reservation;
use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\Connection;
use meican\circuits\forms\AuthorizationForm;
use meican\circuits\models\ReservationNotification;
use meican\aaa\models\User;

/**
 */
class AuthorizationSearch extends AuthorizationForm{

    public $src_domain;
    public $dst_domain;
	public $domain;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['src_domain', 'dst_domain', 'domain'], 'safe'],
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

    public function searchByDomains($params){
    	$this->load($params);
    	
    	$userId = Yii::$app->user->getId();
    
    	$now = DateUtils::now();
    
    	$authorizations = []; //Armazena os pedidos
    	$reservationsVisited = []; //Armazena as reservas ja incluidas nos pedidos e o dominio ao qual o pedido foi feito.
    	 
    	//Pega todas requisições feitas para o usuário
    	
    	if($this->domain) $userRequests = ConnectionAuth::find()->where(['domain' => $this->domain, 'manager_user_id' => $userId, 'status' => Connection::AUTH_STATUS_PENDING])->all();
    	else $userRequests = ConnectionAuth::find()->where(['manager_user_id' => $userId, 'status' => Connection::AUTH_STATUS_PENDING])->all();
    	foreach($userRequests as $request){ //Limpa mantendo apenas 1 por reserva
    		$uniq = true;
    		$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['<=','start', DateUtils::now()])->one();
    		if(isset($conn)){
    			$request->changeStatusToExpired();
    			$conn->auth_status= Connection::AUTH_STATUS_EXPIRED;
    			$conn->save();
    			ReservationNotification::create($conn->id);
    		}
    		else{
    			$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['>','start', DateUtils::now()])->one();
    			foreach($reservationsVisited as $res){
    				if($conn->reservation_id == $res[0] && $request->domain == $res[1]){
    					$uniq = false;
    				}
    			}
    			if($uniq){
    				$aux = [];
    				$aux[0] = $conn->reservation_id;;
    				$aux[1] = $request->domain;
    				$reservationsVisited[] = $aux;
    
    				$source = $conn->getFirstPath()->one();
    				$destination = $conn->getLastPath()->one();
    				
    				if(!$this->src_domain || $this->src_domain == $source->domain){
    					if(!$this->dst_domain || $this->dst_domain == $destination->domain){
		    				$form = new AuthorizationForm;
		    				$form->setValues(Reservation::findOne(['id' => $conn->reservation_id]), $request->domain, $source->domain, $destination->domain);
		    				$authorizations[] = $form;
    					}
    				}
    			}
    		}
    	}
    
    	//Pega todos os papeis do usuário
    	$domainRoles = User::findOne(['id' => $userId])->getRoles()->all();
    	foreach($domainRoles as $role){ //Passa por todos papeis
    		if($this->domain) $groupRequests = ConnectionAuth::find()->where(['domain' => $this->domain, 'manager_group_id' => $role->getGroup()->id, 'status' => Connection::AUTH_STATUS_PENDING])->all();
    		else $groupRequests = ConnectionAuth::find()->where(['manager_group_id' => $role->getGroup()->id, 'status' => Connection::AUTH_STATUS_PENDING])->all();
    		foreach($groupRequests as $request){ //Passa por todas requisições para testar se o dominio corresponde
    			$domain = Domain::findOne(['name' => $request->domain]);
    			if($domain){
    				if($role->domain == NULL || $role->domain == $domain->name){
    					$uniq = true;
    					$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['<=','start', DateUtils::now()])->one();
    					if(isset($conn)){
    						$request->changeStatusToExpired();
    						$conn->auth_status= Connection::AUTH_STATUS_EXPIRED;
    						$conn->save();
    						ReservationNotification::create($conn->id);
    					}
    					else{
    						$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['>','start', DateUtils::now()])->one();
    						foreach($reservationsVisited as $res){
    							if($conn->reservation_id == $res[0] && $domain->name == $res[1]){
    								$uniq = false;
    							}
    						}
    						if($uniq){
    							$aux = [];
    							$aux[0] = $conn->reservation_id;;
    							$aux[1] = $request->domain;
    							$reservationsVisited[] = $aux;
    								
    							$source = $conn->getFirstPath()->one();
    							$destination = $conn->getLastPath()->one();
    							if(!$this->src_domain || $this->src_domain == $source->domain){
    								if(!$this->dst_domain || $this->dst_domain == $destination->domain){
		    							$form = new AuthorizationForm;
		    							$form->setValues(Reservation::findOne(['id' => $conn->reservation_id]), $request->domain, $source->domain, $destination->domain);
		    							$authorizations[] = $form;
    								}
    							}
    						}
    					}
    				}
    			}
    		}
    	}
    	
    	$dataProvider = new ArrayDataProvider([
    			'allModels' => $authorizations,
    			'sort' => false,
    			'pagination' => [
    					'pageSize' => 15,
    			],
    	]);
    	 
    	return $dataProvider;
    }
    
}