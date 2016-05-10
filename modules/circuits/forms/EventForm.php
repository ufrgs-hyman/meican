<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class EventForm extends Model {
    
    public $date;

    public function rules() {
        return [
            [['date'],'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'date' =>  Yii::t("circuits", 'Date and time range'),
        ];
    }
}