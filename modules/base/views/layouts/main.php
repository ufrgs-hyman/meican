<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Html;
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
<body class="sidebar-mini skin-blue-light">
<?php $this->beginBody() ?>
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="<?= Url::base(); ?>/home" class="logo" title="MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><?= Html::img(Url::base()."/images/meican_logo_23.png", ['title'=>'MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks']); ?></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><?= Html::img(Url::base()."/images/meican_branco.png", ['title'=>'MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks']); ?></span>

    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <?= $this->render('navbar-menu'); ?>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <section class="sidebar">
      <?= $this->render('sidebar-menu'); ?>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. -->
  <div class="content-wrapper">
    <?php 
        if (isset($this->params['header'])) {
            if(count($this->params['header']) > 1) {
                $route = $this->params['header'][1][0];
                for ($i=1; $i < count($this->params['header'][1]); $i++) { 
                    $route .= " > ".$this->params['header'][1][$i];
                }
                echo '<section class="content-header">
                    <h1>
                      '.$this->params['header'][0].'
                      <small>'.$route.'</small>
                    </h1>
                  </section>';
            } else {
                echo '<section class="content-header">
                    <h1>
                      '.$this->params['header'][0].'
                    </h1>
                  </section>';
            }
        } 

        if (!isset($this->params['hide-content-section'])) {
            echo '<section class="content">';
            echo $content;
            echo '</section>';
        } else {
            echo $content;
        }
    ?>
  </div>
  <!-- /.content-wrapper -->

    <?php if(!isset($this->params['hide-footer'])): ?>
    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="pull-right hidden-xs">
          Version <b><?= Yii::$app->version; ?></b>
        </div>
        <!-- Default to the left -->
        <strong>&copy; 2023 <a href="http://www.rnp.br" target="_blank">RNP</a></strong>
    </footer>
    <?php endif; ?>

</div>
<!-- ./wrapper -->

<?php $this->endBody() ?>
<?php 

foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    switch($key){
      case 'success':
        $icon='glyphicon glyphicon-ok-sign';
        break;
      case 'warning':
        $icon='glyphicon glyphicon-exclamation-sign';
        break;
      case 'danger':
        $icon='glyphicon glyphicon-ban-circle';
        break;
      default:
        $icon='glyphicon glyphicon-ok-sign';
    }
    echo Growl::widget([
        'type' => $key,
        'icon' => $icon,
        'body' => $message,
        'pluginOptions' => [
            'delay' => 10000,
            'newest_on_top' => true,
            'placement' => [
                'from' => 'top',
                'align' => 'right',
            ],
            'offset' => [
                'y'=>60,
                'x'=>20
            ]]
    ]);
}

Modal::begin([
    'id' => 'feedback-modal',
    'header' => 'Feedback',
    'footer' => '<button class="send-btn btn btn-primary">Send</button><button class="cancel-btn btn btn-default">Cancel</button>',
]); 

$form = \yii\bootstrap\ActiveForm::begin(['id'=>'feedback-form', 'layout' => 'horizontal']);
$model = new FeedbackForm;

echo $form->field($model, 'subject')->textInput(); 
echo $form->field($model, 'message')->textArea(['rows'=> 6, 'resize' => false]); 

\yii\bootstrap\ActiveForm::end();

Modal::end(); ?>

<?= Analytics::build(); ?>
</body>
<?php $this->endPage() ?>
</html>
