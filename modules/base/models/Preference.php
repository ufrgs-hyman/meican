<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\models;

use Yii;

/**
 * Generic preference entity.
 *
 * @property string $name
 * @property string $value
 *
 * @author Maurício Quatrin Guerreiro
 */
class Preference extends \yii\db\ActiveRecord
{
    //retorna o NSA que identifica a aplicação
    const MEICAN_NSA = "meican.nsa";
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%preference}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('circuits', 'Name'),
            'value' => Yii::t('circuits', 'Value'),
        ];
    }

    static function getNames() {
        return [self::MEICAN_NSA];
    }

    static function findAll($condition=null) {
        if ($condition) {
            return self::find()->where(['in', 'name', static::getNames()])->andWhere($condition)->all();
        }
        return self::find()->where(['in', 'name', static::getNames()])->all();
    }

    public function getBoolean() {
        return $this->value == "true" ? true : false;
    }

    static function findOneValue($name) {
        return self::findOne($name)->value;
    }
}
