<?php $args = $this->passedArgs ?>
<?php $base = Dispatcher::getInstance()->url(); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo Framework::getSystemName(); ?></title>

        <!-- GLOBAL JS SCRIPTS AND IN-LINE FUNCTIONS -->
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/style1.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/timePicker.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/style1.css" />
	<link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/themes/start/jquery-ui.css" />

        <script type="text/javascript" src="<?php echo $base; ?>webroot/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $base; ?>webroot/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo $base; ?>webroot/js/jquery.crypt.js"></script>
        <script type="text/javascript" src="<?php echo $base; ?>webroot/js/jquery.form.js"></script>
        <script type="text/javascript" src="<?php echo $base; ?>apps/init/webroot/js/info_box.js"></script>
        <script type="text/javascript" src="<?php echo $base; ?>webroot/js/jquery.pjax.js"></script>


        <!-- ESSE SCRIPT TÁ DANDO PROBLEMA
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/jquery-1.4.2.min.js"></script>
        -->

        <script type ="text/javascript" src="<?php echo $base; ?>apps/topology/webroot/js/devices.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/topology/webroot/js/networks.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/topology/webroot/js/urns.js"></script>

        <script type ="text/javascript" src="<?php echo $base; ?>apps/aaa/webroot/js/password.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/aaa/webroot/js/select.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/aaa/webroot/js/acl.js"></script>

        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/jquery-ui.min.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/googlemaps.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/markerClusterer.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/StyledMarker.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/map.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/reservations.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/reservation_map.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/reservations_add.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/flows.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/timers.js"></script>
        <script type ="text/javascript" src="<?php echo $base; ?>apps/circuits/webroot/js/jquery.timePicker.js"></script>

        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <script type ="text/javascript">
            <?php // variavel para armazenar o ID quando a função setInterval() é usada
            // cada vez que um link é carregado, é feito um clear na variável, para não carregar em páginas erradas?>
            var js_function_interval = null;
            <?php // variavel global para armazenar o retorno de uma função de validação de um formulario, testada dentro do delegate?>
            var js_submit_form = true;
            <?php //url base para geração de url, é o diretório onde o sistema está instalado no servidor ?>
            var baseUrl = '<?php echo $this->url(''); ?>';
        </script>
	<script type ="text/javascript" src="<?php echo $base; ?>webroot/js/main.js"></script>
<?php if ($this->script->scriptArgs): ?>
<script>
<?php
    foreach ($this->script->scriptArgs as $name => $val) {
            echo "var $name = ".json_encode($val).";";
    }?>
</script>
<?php endif; ?>
<?php if ($this->script->jsFiles): ?>
    <?php foreach ($this->script->jsFiles as $f) {
        echo '<script type ="text/javascript" src="'.Dispatcher::getInstance()->url('').$f.'"></script>';
    } ?>
<?php endif; ?>	

<?php /*  Coloca o theme roller
<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/base/jquery-ui.css" />
<script>$(document).ready(function(){$('#switcher').themeswitcher();});</script>
<script type="text/javascript" src="http://jqueryui.com/themeroller/themeswitchertool/"></script>
<div id="switcher"></div> */?>
    
    </head>

    <body>
  


        <div id="auxDiv">
        </div>
        <!-- joga dentro dessa tag o html a ser processado - o que retorna do ajax -->
        <div id="htmlToLoad" style="display: none"></div>
            <div id="header" class="header">
                <div id="logo_box">
                    <a href="<?php echo $this->buildLink(array('action' => 'welcome')); ?>"><img class="logo" alt="MEICAN" src="<?php echo $this->url(''); ?>webroot/img/meican_white.png"/></a>
                </div>

                <div id="info_box">
                    <?php echo $this->element('info_box', array('app' => 'init'));?>
                </div>
            </div>
            <div id="flash_box" class="shadow">
                <?php if ($content_for_flash): ?>
                <?php foreach ($content_for_flash as $f) : ?>
                    <?php
                    $ar = explode(":", $f);
                    $status = $ar[0];
                    $message = $ar[1];
                    ?>
                    <div class="<?php echo $status; ?>"><?php echo $message; ?>
                        <input type="button" class="closeFlash" onclick="clearFlash();"/>
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>

            </div>
            <div id="content">
                <div id="menu">
                    <?php echo $this->element('menu', array('app' => 'init'));?>
                </div>
                <div id="load_img" style="display: none">
                    <img src="<?php echo $base; ?>webroot/img/ajax-loader_1.gif" alt="<?php echo _('Loading'); ?>"/>
                </div>
                <div id="main">
                    <?php echo $content_for_body; //debug($this->script->jsFiles);?>
                </div>
            </div>
           <!-- <div id="footer">
                <img src="<?php echo $this->url(''); ?>webroot/img/footer.png" style="width:100%; position: absolute; height: 25px;"></img>
            </div>-->
    </body>


</html>
