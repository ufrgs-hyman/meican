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

    public function run() {
        return $this->render('@meican/base/views/_grid-buttons', 
          ['addUrl'=> Url::to((isset($this->addRoute) ? $this->addRoute : 'create'))]);
    }

}

?>