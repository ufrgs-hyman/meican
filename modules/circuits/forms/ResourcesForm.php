<?php

namespace meican\circuits\forms;

use Yii;
use yii\base\Model;

class ResourcesForm extends Model
{
    public $container_name;
    public $container_port;

    public function rules()
    {
        return [
            [['container_name', 'container_port'], 'required'],
            [['container_port'], 'number', 'min' => 0, 'max' => 65535],
        ];
    }

    public function attributesLabels()
    {
        return [
            'container_name' => 'Container Name',
            'container_port' => 'Container Port',
        ];
    }
}