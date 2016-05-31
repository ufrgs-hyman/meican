<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Html;
use yii\bootstrap\Modal;

\meican\bpm\assets\IndexCreate::register($this);

?>

	<div id="dialog" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                	<h5><?= Yii::t("bpm", 'Please select the Domain for which you want create a Workflow:');?></h5>
                    <select id="selectDomain" class="form-control"></select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="button_ok" class="btn btn-primary">Ok</button>
                    <button type="button" id="button_cancel" class="btn btn-default" data-dismiss="modal"><?= Yii::t("bpm", 'Cancel');?></button>
                </div>
            </div>
        </div>
    </div>

<?php 