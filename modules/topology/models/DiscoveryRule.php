<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\models;

use Yii;

use meican\base\components\DateUtils;
use meican\topology\components\NSIParser;
use meican\topology\components\NMWGParser;
use meican\topology\models\TopologyNotification;

/**
 * Esta classe representa uma regra de descobrimento.
 * Ela define onde, quando e como uma consulta (DiscoveryQuery)
 * deve proceder para descobrir topologias. 
 *
 * @property integer $id
 * @property string $name
 * @property string $protocol
 * @property string $type
 * @property string $provider_nsa
 * @property string $subscription_id
 * @property string $url
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryRule extends \yii\db\ActiveRecord
{
    const PROTOCOL_HTTP = "HTTP";
    const PROTOCOL_NSI_DS = "NSI_DS_1_0";
    const DESC_TYPE_NSI = "NSI_TD_2_0_NSAD_1_0";
    const DESC_TYPE_NMWG = "NMWG_TD_3_0";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topo_synchronizer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'auto_apply', 'name', 'url', 'protocol'], 'required'],
            [['provider_nsa'], 'unique', 'message'=> 'Only one Discovery Service is allowed for each NSI Provider. The NSA ID "{value}" has already in use.'],
            [['type'], 'string'],
            [['auto_apply'], 'boolean'],
            [['name', 'subscription_id'], 'string', 'max' => 200],
            [['url', 'provider_nsa'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topology', 'ID'),
            'auto_apply' => Yii::t('topology', 'Apply method'),
            'type' => Yii::t('topology', 'Type'),
            'subscription_id' => Yii::t('topology', 'Autosync by notification'),
            'name' => Yii::t('topology', 'Name'),
            'url' => Yii::t('topology', 'URL'),
            'provider_nsa' => Yii::t('topology', 'Provider NSA ID'),
        ];
    }

    public function getQueries() {
        return DiscoveryQuery::find()->where(['sync_id'=> $this->id])->orderBy(['started_at'=> SORT_DESC]);
    }

    public function getLastSyncDate() {
        $event = $this->getEvents()->select(['started_at'])->asArray()->one();
        return $event ? $event['started_at'] : null;
    }

    public function isAutoSyncEnabled() {
        return false;
    }

    public function getProtocol() {
        switch ($this->protocol) {
            case self::PROTOCOL_NSI_DS:
                return Yii::t('topology', 'NSI Discovery Service 1.0');
            case self::PROTOCOL_HTTP:
                return Yii::t('topology', 'HTTP');
        }
    }

    static function getProtocols() {
        return [
            ['id'=> self::PROTOCOL_NSI_DS, 'name'=> Yii::t('topology', 'NSI Discovery Service 1.0')],
            ['id'=> self::PROTOCOL_HTTP, 'name'=> Yii::t('topology', 'HTTP')],
        ];
    }

    public function getType() {
        switch ($this->type) {
            case self::DESC_TYPE_NSI: return Yii::t('topology', 'NSI Topology 2.0');
            case self::DESC_TYPE_NMWG: return Yii::t('topology', 'NMWG Topology 3.0');
        }
    }

    static function getTypes() {
        return [
            ['id'=> self::DESC_TYPE_NSI, 'name'=> Yii::t('topology', 'NSI Topology 2.0')],
            ['id'=> self::DESC_TYPE_NMWG, 'name'=> Yii::t('topology', 'NMWG Topology 3.0')],
        ];
    }

    static function findOneByNsa($nsa) {
        return self::find()->where(['provider_nsa'=>$nsa])->one();
    }
}
