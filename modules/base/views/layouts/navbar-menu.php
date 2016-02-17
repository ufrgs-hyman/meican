<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

use yii\helpers\Html;
use yii\helpers\Url;
use meican\notification\models\Notification;

?>

<ul class="nav navbar-nav">
  <!-- Messages: style can be found in dropdown.less-->
  <?php if (!\Yii::$app->user->isGuest): ?>
    
  <!-- Notifications Menu -->
  <li class="dropdown messages-menu">
    <!-- Menu toggle button -->
    <a id="not_toggle_button" href="#" class="dropdown-toggle" data-toggle="dropdown">
      <i class="fa fa-bell-o"></i>
      <span id="not_number" class="label label-warning"></span>
    </a>
    <ul id="not_body" class="dropdown-menu">
      <li class="header"><?= Yii::t("notification", "You have {number} notifications" ,['number'=>Notification::getNumberNotifications()])?></li>
      <li id="not_content_li">
        <!-- Inner Menu: contains the notifications -->
        <ul id="not_content" class="menu">
        </ul>
		<?= Html::img('@web'.'/images/ajax-loader.gif', ['id' => "not_loader", 'style'=>'padding: 10px;']); ?>
      </li>
      <li class="footer"><a href="#">View All</a></li>
      <li class="footer"><?= Html::a(Yii::t("notification", "View Authorizations")." (<span id='authN'>".Notification::getNumberAuthorizations()."</span>)",array('/circuits/authorization/index')); ?></li>
    </ul>
  </li>
  
  <?php endif; ?>
  <li><?= Html::a(Yii::t("home", "About"),array('/home/support/about')); ?></li>
  <li><?= Html::a(Yii::t("home", "Help"),array('/home/support/help')); ?></li>

  <?php if (!\Yii::$app->user->isGuest): ?>
  <!-- User Account Menu -->
  <li class="dropdown user user-menu">
    <!-- Menu Toggle Button -->
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <!-- The user image in the navbar-->
      <?= Html::img("@web/images/avatar.png", ['class'=> 'user-image']); ?>
      <!-- hidden-xs hides the username on small devices so only the image appears. -->
      <span class="hidden-xs"><?= \Yii::$app->user->getIdentity()->name; ?></span>
    </a>
    <ul class="dropdown-menu">
      <!-- The user image in the menu -->
      <li class="user-header">
        <?= Html::img("@web/images/avatar.png", ['class'=> 'img-circle']); ?>

        <p>
          <?= \Yii::$app->user->getIdentity()->name; ?>
          <small><?= \Yii::$app->user->getIdentity()->email; ?></small>
        </p>
      </li>
      
      <!-- Menu Footer-->
      <li class="user-footer">
        <div class="pull-left">
          <a href="<?= Url::toRoute(['/aaa/user/account']); ?>" class="btn btn-default">My account</a>
        </div>
        <div class="pull-right">
          <a href="#" class="btn btn-default">Feedback</a>
        </div>
      </li>
    </ul>
  </li>
  <li><?= Html::a(Yii::t("home", "Sign out"),array('/aaa/login/logout')); ?></li>
  <?php else: ?>
  <li><?= Html::a(Yii::t("home", "Sign in"),array('/aaa/login')); ?></li>
  <?php endif; ?>
</ul>