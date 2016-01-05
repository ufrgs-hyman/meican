<?php
use yii\helpers\Html;
use yii\helpers\Url;

use meican\base\assets\layout\Asset;
use meican\base\widgets\Analytics;

/* @var $this \yii\web\View */
/* @var $content string */

Asset::register($this);

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= Html::encode(Yii::$app->name); ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?= Html::csrfMetaTags() ?>
  <link rel="shortcut icon" href="<?= Url::base(); ?>/images/favicon.ico" type="image/x-icon" />
  <?php $this->head() ?>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<?php $this->beginBody() ?>
<?= $content; ?>
<!-- /.login-box -->

<?php $this->endBody() ?>
<?= Analytics::build(); ?>
</body>
<?php $this->endPage() ?>
</html>
