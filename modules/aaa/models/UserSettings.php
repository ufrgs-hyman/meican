<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\models;

use Yii;

/**
 * This is the model class for table "{{%usersettings}}".
 *
 * @property integer $id
 * @property string $topo_viewer
 *
 * @property User $user
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
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
