<?php 

use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\bootstrap\Button;

use meican\base\assets\grid\ButtonsAsset;

ButtonsAsset::register($this);

?>

<div>
	<a class="btn btn-primary" href="<?= Url::toRoute("create"); ?>">Add</a>
    <a id="delete-grid-btn" class="btn btn-warning">Delete</a>
</div>

<?php Modal::begin([
    'id' => 'delete-grid-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="cancel-grid-btn" class="btn btn-default">Cancel</button> <button id="confirm-grid-btn" class="grid-btn btn btn-danger">Delete</button>',
]);

echo 'Do you want delete the selected items?';

Modal::end(); ?>

<?php Modal::begin([
    'id' => 'error-grid-modal',
    'headerOptions' => ['hidden'=>'hidden'],
    'footer' => '<button id="close-grid-btn" class="grid-btn btn btn-default">Close</button>',
]);

echo 'Please, select a item.';

Modal::end(); ?>

