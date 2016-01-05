<?php 

namespace meican\base\widgets;

use yii\helpers\Url;
use Yii;

class GridButtons extends \yii\base\Widget {

    public $addRoute;

    public function run() {
        return $this->render('@meican/base/views/_grid-buttons', 
          ['addUrl'=> Url::to((isset($this->addRoute) ? $this->addRoute : 'create'))]);
    }

}

?>