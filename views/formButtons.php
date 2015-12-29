<?php 

use yii\helpers\Url;

?>

<div>
	<a class="btn btn-primary" href="<?= Url::toRoute("create"); ?>">Add</a>
	<button id="delete-button" type="submit" class="btn btn-warning">Delete</button>
</div>
