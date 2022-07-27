<?php
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\models;

use Yii;

/**
 * This is the model class for table "{{%device_type}}".
 *
 * @property integer $id
 * @property string $name

 * @author Lorenzo Costa Lattuada / Arthur Oliveira De Rosso
 */
class Type extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
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
        ];
    }

    

    //findOne com asArray e select integrados
    static function findOneArraySelect($id, $array) {
        return self::find()->where(['id'=>$id])->asArray()->select($array)->one();
    }

    static function findByName($name) {
        return self::find()->where(['name'=>$name]);
    }
}
