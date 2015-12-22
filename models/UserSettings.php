<?php

namespace meican\models;

use Yii;

/**
 * This is the model class for table "{{%usersettings}}".
 *
 * @property integer $id
 * @property string $language
 * @property string $date_format
 * @property string $time_zone
 * @property integer $user_id
 * @property string $name
 * @property string $email
 * @property string $topo_viewer
 *
 * @property User $user
 */
class UserSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language','name','email'], 'required'],
            [['language'], 'string'],
            [['date_format', 'time_zone', 'topo_viewer'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 60],
        	[['email'], 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'language' => 'Language',
            'date_format' => 'Date Format',
            'time_zone' => 'Time Zone',
            'name' => 'Name',
            'email' => 'E-mail',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }

    static function findByEmail($email) {
        return static::find()->where(['email'=>$email]);
    }
}
