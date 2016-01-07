<?php

use yii\widgets\DetailView;
use yii\bootstrap\Html;

?>

<?= Html::img("@web/images/avatar.png", ['class'=> 'profile-user-img img-responsive img-circle']); ?>

<p class="text-muted text-center"><?= $model->name; ?></p>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'created_at',
        'login',               
        'email',
        'language',
        'time_zone',
        'time_format',
        'date_format',
    ],
]); ?>