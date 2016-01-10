<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\models;

use Yii;

/**
 * This is the model class for table "{{%provider_peering}}".
 *
 * @property integer $src_id
 * @property integer $dst_id
 *
 * @property Provider $dst
 * @property Provider $src
 */
class Peering extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%provider_peering}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['src_id', 'dst_id'], 'required'],
            [['src_id', 'dst_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'src_id' => Yii::t('topology', 'Src ID'),
            'dst_id' => Yii::t('topology', 'Dst ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestination()
    {
        return $this->hasOne(Provider::className(), ['id' => 'dst_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Provider::className(), ['id' => 'src_id']);
    }
}
