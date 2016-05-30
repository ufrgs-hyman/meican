<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\home\forms;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class FeedbackForm extends \yii\base\Model
{
    public $subject;
    public $message;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'message'], 'required'],
            [['subject', 'message'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subject' => Yii::t('home', 'Subject'),
            'message' => Yii::t('home', 'Message'),
        ];
    }
}
