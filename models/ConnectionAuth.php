<?php

namespace app\models;

use Yii;
use app\models\Connection;
use app\models\User;
use app\models\Group;

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
    	return $this->status == "AUTHORIZED" || $this->status == "DENIED" || $this->status == "EXPIRED";
    }
    
}