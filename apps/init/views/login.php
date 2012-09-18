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
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/style1.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/login1.css" />
        <?php /*
          <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/style1.css" /> */ ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/meican3-theme/jquery-ui-1.8.16.custom.css" />
        <script type="text/javascript" src="<?php echo $this->url(''); ?>webroot/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $base; ?>webroot/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->url(''); ?>webroot/js/main.js"></script>
        <script type="text/javascript">
            var baseUrl = '<?php echo $this->url(''); ?>';
            $(function(){$.makeAutofocus(); });
        </script>
    </head>
    <body>
        <div id="header" class="header">
            &nbsp;
        </div>
        
        <div id="content">
            
            <div id="text_info" class="main_info">
                <div class="center"><img src="<?php echo $this->url(''); ?>webroot/img/meican_new.png" class="logo" alt="MEICAN"/></div>
                <h2>Management Environment of Inter-domain Circuits for Advanced Networks</h2>

<p>MEICAN allows network end-users to request, in a more user-friendly way, dedicated circuits in Dynamic Circuit Networks. MEICAN also enables network operators to evaluate and accept end-user's circuit requests in environments with multiple domains.
With MEICAN, you can:</p>

<ul>
    <li>
        <b>Request Circuits</b>
        <p>Network end-user's circuits can be scheduled to be set up and teared down when it is more convenient.</p>
    </li>
    <li>
        <b>Authorize Requests</b>
        <p>Network operators can be notified to accept or reject the requests of establishment of new circuits.</p>
    </li>
    <li>
        <b>Build Automated Policies</b>
        <p>Authorization workflows can be used to automate the decision-making process along the multiple domains where end-user's circuits pass through.</p>
    </li>
</ul>



            </div>
            
            <div id="login_box">
                <div id="login_form" class="tab_content">
                    <p>
                        <img src="<?php echo $this->url(''); ?>webroot/img/meican_preto.png" alt="meican"/>
                    </p>
                    <div id="message"><?php echo @$message ?></div>
                    <form name="login_form" method="post" action="<?php echo $this->buildLink(array('action' => 'doLogin')); ?>" class="login">
                        <div style="width: 100%;">
                            <div class="input">
                                <label for="login"><?php echo _('Login'); ?></label>
                                <input class="text" type="text" name="login" id="login" autofocus tabindex="1"/>
                            </div>
                            <div class="input password">
                                <label for="password"><?php echo _('Password'); ?></label>
                                <a href="#" tabindex="5">(<?php echo _('Forgot your password?'); ?>)</a>
                                <input class="text" type="password" name="password" id="password" tabindex="1">
                            </div>
                            <div class="submit">
                                <input class="next ui-button ui-widget ui-state-default ui-corner-all" type="submit" name="submit_login" value="<?php echo _('Sign in'); ?>" role="button" aria-disabled="false" tabindex="3"/>
                                <?php echo _('or'); ?> 
                                <a href="#" tabindex="4"><?php echo _('New account'); ?> &raquo;</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="logos-footer">
                 <a href="http://www.rnp.br" title="RNP"><img src="<?php echo $this->url(''); ?>webroot/img/rnp.gif" alt="RNP" style="height:36px;"/></a>
                 <a href="http://networks.inf.ufrgs.br/" title="Computer Networks UFRGS"><img src="<?php echo $this->url(''); ?>webroot/img/networks.jpg" alt="networks" style="height:42px;"/></a>
                </div>
            </div>
        </div>
        
        <div id="footer">
            
        </div>
        
    </body>
</html>
