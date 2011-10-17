<?php $message= $this->passedArgs; ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url(); ?>webroot/css/login1.css" />
        <title><?php echo Framework::getSystemName(); ?></title>
        <script language="JavaScript1.2" src="<?php echo $this->url(''); ?>webroot/js/jquery.min.js"></script>
        <script language="JavaScript1.2" src="<?php echo $this->url(''); ?>webroot/js/jquery.crypt.js"></script>
        <script language="JavaScript">

            $(document).ready(function(){
                $("#login").focus();

                $('form').submit(function() {
                    $.each($(':password'), function() {
                        var md5 = $(this).crypt({method:"md5"});
                        $(this).attr({style: 'display: none'});

                        $(this).val(md5);
                    }); //do each
                    return true;
                }); //do form
            }); //do ready

        </script>
    </head>


    <body>
        <div id="header" class="header">
            <div id="logo_box">
                <img src="<?php echo $this->url(''); ?>webroot/img/meican_white.png" class="logo" alt="MEICAN"/>
            </div>
            <div id="info_box">
                <a href="#"><?php echo _('About MEICAN');?></a> |
                <a href="#"><?php echo _('Create an account');?></a> |
                <a href="#"><?php echo _('Support');?></a>
            </div>
        </div>
        <div id="content">
            <div id="figure">
                <img src="<?php echo $this->url(''); ?>webroot/img/logo_login.png" alt="MEICAN">
            </div>
            <div id="text_info">
                
            </div>

           
            <div id="login_form">
                <h3><?php echo _('Sign in to MEICAN'); ?></h3>
                <hr>
                <form name="login_form" method="POST" action="<?php echo $this->buildLink(array('action' => 'doLogin')); ?>">
                    <table>
                        <tr>
                            <td>
                                <?php echo _('Login'); ?>
                            </td>
                            <td>
                                <input class="text" type="text" name="login" id="login"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo _('Password'); ?>
                            </td>
                            <td>
                                <input class="text" type="password" name="password">
                            </td>
                        </tr>
                        <tr>
                            <td>

                            </td>
                            <td>
                                (<a href="#"><?php echo _('Forgot your password?'); ?></a>)
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                    <div id='message'><?php echo $message ?></div>
                            </td>
                            

                        </tr>
                        <tr>
                            <td>

                            </td>
                            <td>
                                <input class="next"  type="submit" name="submit_login" value="<?php echo _('Sign in'); ?>">
                            </td>
                            
                        </tr>
                    </table>
                </form>
            </div>
        </div>
             <div id="footer">
<!--            <a href="#">
                        <?php //echo _('About us'); ?>
            </a> |
            <a href="#">
                        <?php //echo _('Developers'); ?>
            </a> |
            <a href="#">
                        <?php //echo _('Terms of service'); ?>
            </a> |
            <a href="#">
                        <?php //echo _('Privacy policy'); ?>
            </a>
            <br>
            2011
-->
        </div>
    </body>
</html>