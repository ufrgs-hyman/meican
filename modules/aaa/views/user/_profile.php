<?php

use yii\widgets\DetailView;

?>

<img class="profile-user-img img-responsive img-circle" src="" alt="User profile picture">

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