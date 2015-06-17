<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%aggregator}}".
 *
 * @property integer $id
 *
 * @property Provider $id0
 * @property DomainAggregator[] $domainAggregators
 * @property Domain[] $domains
 */
class Aggregator extends \yii\db\ActiveRecord
{
	const PROVIDER_TYPE = Provider::TYPE_AGGREGATOR;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%aggregator}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id','default'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }
    
    public static function resetDefault() {
    	foreach (self::find()->all() as $agg) {
    		$agg->default = null;
    		if (!$agg->save()) return false;
    	}
    	
    	return true;
    }
    
    static function findDefault() {
    	return self::find()->where(['default'=>1]);
    }
    
    public function setProvider($provider) {
    	$this->id = $provider->id;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'id']);
    }

    public function delete() {
    	return Provider::deleteAll(['id'=>$this->id]);
    }
}
