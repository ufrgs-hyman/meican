<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%device}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $ip
 * @property string $trademark
 * @property string $model
 *
 * Localização aproximada
 *
 * @property string $address
 *
 * Coordenadas relativas a localização aproximada
 *
 * @property double $latitude
 * @property double $longitude
 * @property string $node
 * @property integer $domain_id
 *
 * @property Domain $domain
 * @property Port[] $ports
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['latitude', 'longitude'], 'number'],
            [['domain_id'], 'required'],
            [['domain_id'], 'integer'],
            [['name', 'trademark', 'model', 'node'], 'string', 'max' => 50],
            [['ip'], 'string', 'max' => 16],
            [['address'], 'string', 'max' => 200],
            [['node', 'domain_id'], 'unique', 'targetAttribute' => ['node', 'domain_id'], 'message' => 'The combination of Node and Domain ID has already been taken.']
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
            'ip' => Yii::t('topology', 'Ip'),
            'trademark' => Yii::t('topology', 'Trademark'),
            'model' => Yii::t('topology', 'Model'),
        	'address' => Yii::t('topology', 'Address'),
            'latitude' => Yii::t('topology', 'Latitude'),
            'longitude' => Yii::t('topology', 'Longitude'),
            'node' => Yii::t('topology', 'Node'),
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
        return $this->hasMany(Port::className(), ['device_id' => 'id']);
    }

    static function findOneByDomainAndNode($domainName, $node) {
        $dom = Domain::findByName($domainName)->one();
        if ($dom) return self::find()->where(['node'=>$node, 'domain_id'=>$dom->id])->one();
        return null;
    }

    static function findOneParentLocation($id) {
        $dev = Device::findOne($id);
        if (!$dev) return null;
        if ($dev->latitude != null) return $dev;
        foreach ($dev->getPorts()->select(['network_id'])->distinct(true)->all() as $port) {
            $net = $port->getNetwork()->one();
            if ($net && $net->latitude) {
                $dev->latitude = $net->latitude;
                $dev->longitude = $net->longitude;
                return $dev;
            }
        }

        return $dev;
    }
}
