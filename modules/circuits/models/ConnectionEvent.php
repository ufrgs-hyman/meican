<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\models;

use Yii;

/**
 * @property integer $id
 * @property integer $conn_id
 * @property string $created_at
 *
 * @property Connection $conn
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ConnectionEvent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%connection_event}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conn_id', 'created_at'], 'required'],
            [['conn_id'], 'integer'],
            [['create_at'], 'safe'],
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
            'created_at' => Yii::t('circuits', 'Date'),
            'author_id' => Yii::t("circuits", "Author"),
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
