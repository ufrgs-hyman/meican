<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property integer $id
 * @property integer $provider_id
 * @property string $type
 * @property string $url
 *
 * @property Provider $provider
 */
class Service extends \yii\db\ActiveRecord
{
    const TYPE_NSI_DISCOVERY = "NSI_DISCOVERY";
    const TYPE_NSI_TOPOLOGY = "NSI_TOPOLOGY";
    const TYPE_NSI_CONNECTION = "NSI_CONNECTION";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider_id', 'type', 'url'], 'required'],
            [['provider_id'], 'integer'],
            [['type'], 'string'],
            [['url'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('circuits', 'ID'),
            'provider_id' => Yii::t('circuits', 'Provider ID'),
            'type' => Yii::t('circuits', 'Type'),
            'url' => Yii::t('circuits', 'URL'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }
}
