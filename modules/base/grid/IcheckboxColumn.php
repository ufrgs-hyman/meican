<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\grid;

use yii\grid\CheckboxColumn;
use yii\bootstrap\Html;

/**
 * iCheck style for yii2 CheckboxColumn
 *
 * @author Mauricio Quatrin Guerreiro
 * @since 2.3.0
 */
class IcheckboxColumn extends CheckboxColumn {

    public function init() {
        parent::init();
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->checkboxOptions instanceof Closure) {
            $options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        } else {
            $options = $this->checkboxOptions;
            if (!isset($options['value'])) {
                $options['value'] = is_array($key) ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $key;
            }
        }
        if(isset($options['class'])) {
            $options['class'] .= ' icheck'; 
        } else {
            $options['class'] = 'icheck';
        }

        $options['hidden'] = true;

        return Html::checkbox($this->name, !empty($options['checked']), $options);
    }
}