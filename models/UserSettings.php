<?php

namespace meican\models;

use Yii;

/**
 * This is the model class for table "{{%usersettings}}".
 *
 * @property integer $id
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
            [['topo_viewer'], 'string', 'max' => 40],
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }
}
