<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use app\controllers\RbacController;

?>

<div class="dashboard">
		<?php if(RbacController::can("reservation/create")): ?>
        <div>
            <h2><?= Yii::t('init', 'New Reservation'); ?></h2>
            <a href="<?= Url::to(['/circuits/reservation/create']); ?>">
                <img src="<?= Url::to('@web/images/new_reservation.png'); ?>" alt="New Reservation"/>
            </a>
        </div>
        <?php endif; ?>
        <?php if(RbacController::can("reservation/read")): ?>
         <div>
            <h2><?= Yii::t('init', 'Reservations'); ?></h2>
            <a href="<?= Url::to(['/circuits/reservation/status']); ?>">
                <img src="<?= Url::to('@web/images/reservations_list.png'); ?>" alt="Reservations"/>
            </a>
        </div>
        <?php endif; ?>
        <?php if(RbacController::can("user/read")): ?>
         <div>
            <h2><?= Yii::t('init', 'Users'); ?></h2>
            <a href="<?= Url::to(['/aaa/user']); ?>">
                <img src="<?= Url::to('@web/images/management.png'); ?>" alt="Users"/>
            </a>
        </div>
        <?php endif; ?>
        <?php if(RbacController::can("reservation/read")): ?>
         <div>
            <h2><?= Yii::t('init', 'Requests'); ?></h2>
            <a href="<?= Url::to(['/circuits/authorization/index']); ?>">
                <img src="<?= Url::to('@web/images/requests_1.png'); ?>" alt="Requests"/>
            </a>
        </div>
        <?php endif; ?>
</div>