<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\models;

use Yii;

/**
 * This is the model class for table "{{%connection_log}}".
 *
 * @property integer $id
 * @property integer $conn_id
 * @property string $date
 * @property integer $received
 * @property string $message
 *
 * @property Connection $conn
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ConnectionLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%connection_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conn_id', 'date', 'received', 'message'], 'required'],
            [['conn_id', 'received'], 'integer'],
            [['date'], 'safe'],
            [['message'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('circuits', 'ID'),
            'conn_id' => Yii::t('circuits', 'Conn ID'),
            'date' => Yii::t('circuits', 'Date'),
            'received' => Yii::t('circuits', 'received'),
            'message' => Yii::t('circuits', 'Message'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnection()
    {
        return $this->hasOne(Connection::className(), ['id' => 'conn_id']);
    }
}
