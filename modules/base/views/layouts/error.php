<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

use kartik\growl\Growl;

use meican\base\widgets\Analytics;
use meican\home\forms\FeedbackForm;

\meican\base\assets\Theme::register($this);

?>
<!DOCTYPE html>
<?php $this->beginPage() ?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= Html::encode(Yii::$app->name); ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?= Html::csrfMetaTags() ?>
    <link rel="shortcut icon" href="<?= Url::base(); ?>/images/favicon.ico" type="image/x-icon" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
        window.baseUrl = "<?= Url::base(); ?>";
        var language = "<?= Yii::$app->language; ?>";
    </script>
    <?php $this->head() ?>
</head>
<body class="sidebar-mini skin-blue-light sidebar-collapse">
<?php $this->beginBody() ?>
<div class="wrapper">

  <header class="main-header">

    <a href="/home" class="logo" title="MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks">
      <span class="logo-lg"><?= Html::img(Url::base()."/images/meican_branco.png", ['title'=>'MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks']); ?></span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">
      <div class="navbar-custom-menu">
      </div>
    </nav>
  </header>

  <div class="content-wrapper">
    <?php 
        echo '<section class="content">';
        echo $content;
        echo '</section>';
    ?>
  </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
          Version <b><?= Yii::$app->version; ?></b>
        </div>
        <strong>&copy; 2023 <a href="https://www.rnp.br" target="_blank">RNP</a></strong>
    </footer>

</div>

<?php $this->endBody() ?>

<?= Analytics::build(); ?>
</body>
<?php $this->endPage() ?>
</html>
