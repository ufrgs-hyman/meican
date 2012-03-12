<?php $base = $this->url(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?php echo Configure::read('systemName');
; ?></title>
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
            $(document).ready(function(){
                $("#login").focus();
                $("body").uify();
            }); //do ready

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
<?php /*

            <div id="login_form" class="tab_content">
                <h2 style="padding: 6px 0;"><?php echo _('Sign in to MEICAN'); ?></h2>
                <hr/>
                <form name="login_form" method="post" action="<?php echo $this->buildLink(array('action' => 'doLogin')); ?>">
                    <div>
                        <label for="login"><?php echo _('Login'); ?></label>
                        <input class="text" type="text" name="login" id="login"/>
                    </div>
                    <div>
                        <label for="password"><?php echo _('Password'); ?></label>
                        <input class="text" type="password" name="password" id="password"/>
                    </div>
                    <p>(<a href="#"><?php echo _('Forgot your password?'); ?></a>)</p>
                    <div id='message'><?php echo $message ?></div>
                    <input class="next ui-button ui-widget ui-state-default ui-corner-all"  type="submit" name="submit_login" value="<?php echo _('Sign in'); ?>"/>
                </form>
            </div> */?>
            
            <div id="login_box" style="width: 390px; float: right;">
       <div id="login_form" class="tab_content">
                   <img src="<?php echo $this->url(''); ?>/webroot/img/meican_preto.png" alt="meican" style="height: 55px;"/>
                   <div id='message'><?php echo $message ?></div>
               <form name="login_form" method="post" action="<?php echo $this->buildLink(array('action' => 'doLogin')); ?>">
                           <div style="width: 100%;">
                                           <div>
                                                 <label for="login"><?php echo _('Login'); ?></label>
                                         <br/>
                                                 <input class="text" type="text" name="login" id="login">
                                           </div>
                                           <div>
                                               <label for="password"><?php echo _('Password'); ?></label>
                                           <br>
                                               <input class="text" type="password" name="password" id="password">
                                           </div>
                                           <div id="message"></div>
                                           <div>
                                           <input class="next ui-button ui-widget ui-state-default ui-corner-all" type="submit" name="submit_login" value="<?php echo _('Sign in'); ?>" role="button" aria-disabled="false"/> (<a href="#" style=""><?php echo _('Forgot your password?'); ?></a>)
                                           </div>
                               </div>
               </form>
           </div>
</div>
            
        </div>
        <?php /*
          <div id="footer">
          <?php //echo _('About us');  ?>
          </a> |
          <a href="#">
          <?php //echo _('Developers');  ?>
          </a> |
          <a href="#">
          <?php //echo _('Terms of service');  ?>
          </a> |
          <a href="#">
          <?php //echo _('Privacy policy');  ?>
          </a>
          <br>
          2011
          -->
          </div> */ ?>
    </body>
</html>
