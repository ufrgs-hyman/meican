<?php 
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\grid;

use yii\helpers\Url;
use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class GridButtons extends \yii\base\Widget {

    public $addRoute;
    public $size;

    public function run() {
        return $this->render('@meican/base/views/_grid-buttons', 
          [
          'size' => (isset($this->size) && $this->size == 'small') ? 'small' : 'normal',
          'addUrl'=> Url::to((isset($this->addRoute) ? $this->addRoute : ['create']))]);
    }

}

?>