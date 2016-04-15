<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\helpers\Html;
use yii\helpers\Url;

use meican\aaa\RbacController;

?>

<section class="content-header">
  <h1>
    Dashboard
  </h1>
</section>

<section class="content">

  <div class="row" style="text-align: center">
        <div class="col-xs-6 col-sm-3" style="min-width: 250px">
            <h2><?= Yii::t('home', 'Reserve'); ?></h2>
            <a href="<?= Url::to(['/circuits/reservation/create']); ?>">
                <img style="width: 128px; height:128px;" src="<?= Url::to('@web/images/dash_new_reservation.png'); ?>" alt="New Reservation"/>
            </a>
        </div>
        <?php if(RbacController::can("reservation/read")): ?>
        <div class="col-xs-6 col-sm-3" style="min-width: 250px">
            <h2><?= Yii::t('home', 'Circuits'); ?></h2>
            <a href="<?= Url::to(['/circuits/reservation/status']); ?>">
                <img style="width: 128px; height:128px;" src="<?= Url::to('@web/images/dash_reservations.png'); ?>" alt="Reservations"/>
            </a>
        </div>
        <?php endif; ?>
        <?php if(RbacController::can('user/read') || RbacController::can('role/read')): ?>
        <div class="col-xs-6 col-sm-3" style="min-width: 250px">
            <h2><?= Yii::t('home', 'Users'); ?></h2>
            <a href="<?= Url::to(['/aaa/user']); ?>">
                <img style="width: 128px; height:128px;" src="<?= Url::to('@web/images/dash_users.png'); ?>" alt="Users"/>
            </a>
        </div>
        <?php endif; ?>
        <?php if(RbacController::can("reservation/read")): ?>
        <div class="col-xs-6 col-sm-3" style="min-width: 250px">
            <h2><?= Yii::t('home', 'Authorizations'); ?></h2>
            <a href="<?= Url::to(['/circuits/authorization/index']); ?>">
                <img style="width: 128px; height:128px;" src="<?= Url::to('@web/images/dash_authorizations.png'); ?>" alt="Authorizations"/>
            </a>
        </div>
        <?php endif; ?>
    </div>

</section>

