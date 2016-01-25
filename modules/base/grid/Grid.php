<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\grid;

use yii\grid\GridView;

/**
 * GridView customized for MEICAN.
 *
 * Features:
 * - standard layout
 * - responsive template
 *
 * @author Mauricio Quatrin Guerreiro
 * @since 2.3.0
 */
class Grid extends GridView {

    public $layout = "{items}{summary}{pager}";

    public function init() {
        parent::init();
    }

    /**
     * Renders the data models for the grid view.
     */
    public function renderItems()
    {
        return '<div class="table-responsive">'.parent::renderItems().'</div>';
    }
}

?>