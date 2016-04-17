<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\models;

use Yii;

use meican\aaa\models\User;

/**
 * @property integer $id
 * @property integer $conn_id
 * @property string $created_at
 *
 * @property Connection $conn
 *
 * @author Maurício Quatrin Guerreiro
 */
class ConnectionEvent extends \yii\db\ActiveRecord
{
    //usuario solicitou criacao
    const TYPE_USER_CREATE = 'USER_CREATE';
    //usuario solicitou edicao
    const TYPE_USER_UPDATE = 'USER_UPDATE';
    //usuario solicitou cancelamento
    const TYPE_USER_CANCEL = 'USER_CANCEL';
    //meican solicitou criacao/alteracao do circuito
    const TYPE_NSI_RESERVE = 'NSI_RESERVE';
    //provedor confirma recebimento da solicitacao de criacao/alteracao do circuito
    const TYPE_NSI_RESERVE_RESPONSE = 'NSI_RESERVE_RESPONSE';
    //provedor confirmou criacao/alteracao do circuito
    const TYPE_NSI_RESERVE_CONFIRMED = 'NSI_RESERVE_CONFIRMED';
    //provedor rejeitou criacao/alteracao do circuito
    const TYPE_NSI_RESERVE_FAILED = 'NSI_RESERVE_FAILED';
    //provedor reportou que ocorreu um timeout e o circuito expirou
    const TYPE_NSI_RESERVE_TIMEOUT = 'NSI_RESERVE_TIMEOUT';
    //meican solicitou commit do circuito
    const TYPE_NSI_COMMIT = 'NSI_COMMIT';
    //provedor confirmou commit do circuito
    const TYPE_NSI_COMMIT_CONFIRMED = 'NSI_COMMIT_CONFIRMED';
    //provedor rejeitou commit do circuito
    const TYPE_NSI_COMMIT_FAILED = 'NSI_COMMIT_FAILED';
    //meican solicitou provisionamento do circuito
    const TYPE_NSI_PROVISION = 'NSI_PROVISION';
    //provedor confirmou provisionamento do circuito
    const TYPE_NSI_PROVISION_CONFIRMED = 'NSI_PROVISION_CONFIRMED';
    //meican solicitou cancelamento do circuito
    const TYPE_NSI_TERMINATE = 'NSI_TERMINATE';
    //provedor confirmou cancelamento do circuito
    const TYPE_NSI_TERMINATE_CONFIRMED = 'NSI_TERMINATE_CONFIRMED';
    //meican solicitou detalhes do circuito
    const TYPE_NSI_SUMMARY = 'NSI_SUMMARY';
    //provedor enviou detalhes do circuito
    const TYPE_NSI_SUMMARY_CONFIRMED = 'NSI_SUMMARY_CONFIRMED';
    //provedor enviou detalhes do status do dataplane do circuito
    const TYPE_NSI_DATAPLANE_CHANGE = 'NSI_DATAPLANE_CHANGE';

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
            [['conn_id', 'created_at', 'type'], 'required'],
            [['conn_id', 'author_id'], 'integer'],
            [['created_at', 'message', 'data'], 'safe'],
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

    public function getAuthor() {
        switch ($this->type) {
            case self::TYPE_NSI_PROVISION_CONFIRMED:
            case self::TYPE_NSI_DATAPLANE_CHANGE:
            case self::TYPE_NSI_SUMMARY_CONFIRMED:
            case self::TYPE_NSI_RESERVE_FAILED:
            case self::TYPE_NSI_RESERVE_CONFIRMED:
            case self::TYPE_NSI_RESERVE_RESPONSE:
            case self::TYPE_NSI_COMMIT_CONFIRMED:
            case self::TYPE_NSI_COMMIT_FAILED:
                return 'Provider';
            case self::TYPE_NSI_SUMMARY:
            case self::TYPE_NSI_TERMINATE:
            case self::TYPE_NSI_PROVISION:
            case self::TYPE_NSI_COMMIT:
            case self::TYPE_NSI_RESERVE:
                return 'MEICAN';
            case self::TYPE_USER_CANCEL:
            case self::TYPE_USER_UPDATE:
            case self::TYPE_USER_CREATE:
                return $this->hasOne(User::className(), ['id' => 'author_id'])->select(['name'])->asArray()->one()['name'];
            default:
                return 'Error';
                break;
        }
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function setAuthor($userId) {
        $this->author_id = $userId;
        return $this;
    }

    public function getTypeLabel() {
        switch ($this->type) {
            case self::TYPE_NSI_PROVISION_CONFIRMED:
                return 'NSI Provision Confirmed received';
            case self::TYPE_NSI_DATAPLANE_CHANGE:
                return 'NSI Dataplane Status received';
            case self::TYPE_NSI_SUMMARY_CONFIRMED:
                return 'NSI Query Summary Confirmed received';
            case self::TYPE_NSI_RESERVE_FAILED:
                return 'NSI Reserve Failed received';
            case self::TYPE_NSI_RESERVE_CONFIRMED:
                return 'NSI Reserve Confirmed received';
            case self::TYPE_NSI_RESERVE_RESPONSE:
                return 'NSI Reserve Response received';
            case self::TYPE_NSI_COMMIT_CONFIRMED:
                return 'NSI Reserve Commit Confirmed received';
            case self::TYPE_NSI_COMMIT_FAILED:
                return 'NSI Reserve Commit Failed received';
            case self::TYPE_NSI_SUMMARY:
                return 'NSI Query Summary sent';
            case self::TYPE_NSI_TERMINATE:
                return 'NSI Terminate sent';
            case self::TYPE_NSI_PROVISION:
                return 'NSI Provision sent';
            case self::TYPE_NSI_COMMIT:
                return 'NSI Reserve Commit sent';
            case self::TYPE_NSI_RESERVE:
                return 'NSI Reserve sent';
            case self::TYPE_USER_CANCEL:
                return 'Cancel requested';
            case self::TYPE_USER_UPDATE:
                return 'Edit requested';
            case self::TYPE_USER_CREATE:
                return 'Create requested';
        }
    }
}
