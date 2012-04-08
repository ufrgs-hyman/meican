<?php $base = $this->url(); ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo Configure::read('systemName');
; ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="shortcut icon" href="<?php echo $base; ?>webroot/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/login1.css" />
        <?php /*
          <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/style1.css" /> */ ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/meican3-theme/jquery-ui-1.8.16.custom.css" />
        <script type="text/javascript" src="<?php echo $this->url(''); ?>webroot/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $base; ?>webroot/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->url(''); ?>webroot/js/main.js"></script>
        <script type="text/javascript" src="http://updateyourbrowser.net/asn.js"> </script>
        <script type="text/javascript">
            var baseUrl = '<?php echo $this->url(''); ?>';
            $(function(){$.makeAutofocus(); });
        </script>
    </head>
    <body>
        <div id="header" class="header">

            <div id="logo_box">
                <img src="<?php echo $this->url(''); ?>webroot/img/meican_white.png" class="logo" alt="MEICAN"/>
            </div>
            <div id="info_box">
                <ul>
                    <li><a href="#"><?php echo _('Create an account'); ?></a></li>
                    <li><a href="#"><?php echo _('About MEICAN'); ?></a></li>
                    <li><a href="#"><?php echo _('Support'); ?></a></li>
                </ul>
            </div>
        </div>
        <div id="content">
            <div id="figure">
                <img src="<?php echo $this->url(''); ?>webroot/img/logo_login.png" alt="MEICAN"/>
            </div>
            <div id="text_info">
            </div>
            <div class="logo-rnp">
                <img src="<?php echo $this->url(''); ?>webroot/img/rnpmission.gif" alt="MEICAN"/>
            </div>

            <div id="login_box" style="width: 390px; float: right;">
                <div id="login_form" class="tab_content">
                    <p>
                        <img src="<?php echo $this->url(''); ?>webroot/img/meican_preto.png" alt="meican"/>
                    </p>
                    <div id="message"><?php echo @$message ?></div>
                    <form name="login_form" method="post" action="<?php echo $this->buildLink(array('action' => 'doLogin')); ?>">
                        <div style="width: 100%;">
                            <div>
                                <label for="login"><?php echo _('Login'); ?></label>
                                <br/>
                                <input class="text" type="text" name="login" id="login" autofocus/>
                            </div>
                            <div>
                                <label for="password"><?php echo _('Password'); ?></label>
                                <br>
                                <input class="text" type="password" name="password" id="password">
                            </div>
                            <div>
                                <input class="next ui-button ui-widget ui-state-default ui-corner-all" type="submit" name="submit_login" value="<?php echo _('Sign in'); ?>" role="button" aria-disabled="false"/> (<a href="#" style=""><?php echo _('Forgot your password?'); ?></a>)
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </body>
</html>
