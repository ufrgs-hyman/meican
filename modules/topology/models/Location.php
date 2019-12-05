<?php
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\models;

use Yii;

/**
 * This is the model class for table "{{%location}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $domain_id
 *
 * Coordenadas relativas a localização aproximada
 *
 * @property double $lat
 * @property double $lng
 * 
 * @property Port[] $ports
 *
 * @author Rafael Hengen Ribeiro @rafaelhribeiro
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'lat', 'lng', 'domain_id'], 'required'],
            [['lat', 'lng'], 'number'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topology', 'ID'),
            'name' => Yii::t('topology', 'Name'),
            'lat' => Yii::t('topology', 'Latitude'),
            'lng' => Yii::t('topology', 'Longitude'),
            'domain_id' => Yii::t('topology', 'Domain'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPorts()
    {
        return $this->hasMany(Port::className(), ['location_id' => 'id']);
    }

    //findOne com asArray e select integrados
    static function findOneArraySelect($id, $array) {
        return self::find()->where(['id'=>$id])->asArray()->select($array)->one();
    }

    static function findByName($name) {
        return self::find()->where(['name'=>$name]);
    }

    static function findByDomainIdAndName($name, $domain_id) {
        return self::find()->andWhere(['name'=>$name])->andWhere(['domain_id'=>$domain_id])->one();
    }
}
