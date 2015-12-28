<?php

namespace meican\circuits\models;

use Yii;

/**
 * This is the model class for table "meican_connection_auth".
 *
 * @property integer $id
 * @property string $domain
 * @property string $status
 * @property string $type
 * @property string $manager_message
 * @property integer $manager_user_id
 * @property integer $manager_group_id
 * @property integer $manager_workflow_id
 * @property integer $connection_id
 *
 * @property Connection $connection
 * @property Domain $domain
 * @property Group $managerGroup
 * @property User $managerUser
 * @property BpmWorkflow $managerWorkflow
 */
class ConnectionAuth extends \yii\db\ActiveRecord
{
	
	const TYPE_USER = "USER";
	const TYPE_GROUP = "GROUP";
	const TYPE_WORKFLOW = "WORKFLOW";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meican_connection_auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain', 'status', 'type', 'connection_id'], 'required'],
            [['manager_user_id', 'manager_group_id', 'manager_workflow_id', 'connection_id'], 'integer'],
            [['status', 'type'], 'string'],
            [['manager_message'], 'string', 'max' => 200],
        	[['domain'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain' => 'Domain',
            'status' => 'Status',
            'type' => 'Type',
            'manager_message' => 'Manager Message',
            'manager_user_id' => 'Manager User ID',
            'manager_group_id' => 'Manager Group ID',
            'manager_workflow_id' => 'Manager Workflow ID',
            'connection_id' => 'Connection ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnection()
    {
        return $this->hasOne(Connection::className(), ['id' => 'connection_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['name' => 'domain']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagerGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'manager_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'manager_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagerWorkflow()
    {
        return $this->hasOne(BpmWorkflow::className(), ['id' => 'manager_workflow_id']);
    }
    
    /**
     * @return boolean
     */
    public function isAnswered() {
    	return !($this->status == Connection::AUTH_STATUS_PENDING);
    }
    
    public function getStatus(){
    	if($this->status == Connection::AUTH_STATUS_PENDING) return Yii::t('circuits', 'Waiting');
    	else if($this->status == Connection::AUTH_STATUS_APPROVED) return Yii::t('circuits', 'Approved');
    	else if($this->status == Connection::AUTH_STATUS_REJECTED) return Yii::t('circuits', 'Rejected');
    	else if($this->status == Connection::AUTH_STATUS_EXPIRED) return Yii::t('circuits', 'Expired');
    	else if($this->status == Connection::AUTH_STATUS_UNSOLICITED) return Yii::t('circuits', 'Unsolicited');
    	else if($this->status == Connection::AUTH_STATUS_UNEXECUTED) return Yii::t('circuits', 'Unexecuted');
    }
    
    public function changeStatusToExpired(){
    	$this->status = Connection::AUTH_STATUS_EXPIRED;
    	$this->save();
    	
    	BpmFlow::removeFlows($this->connection_id);
    }
    
    public static function cancelConnAuthRequest($connId){
    	$types = [self::TYPE_USER, self::TYPE_GROUP];
    	$auth = ConnectionAuth::find()->where(['connection_id' => $connId, 'status' => Connection::AUTH_STATUS_PENDING])->andWhere(['in', 'type', $types])->one();
    	if($auth){
    		$auth->changeStatusToExpired();
    		$connection = Connection::findOne($connId);
    		$connection->auth_status= Connection::AUTH_STATUS_EXPIRED;
    		$connection->save();
    	}
    }
    
}