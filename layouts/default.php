<?php $args = $this->passedArgs ?>
<?php $base = Dispatcher::getInstance()->base.'/'; ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo Framework::getSystemName(); ?></title>

        <!-- GLOBAL JS SCRIPTS AND IN-LINE FUNCTIONS -->
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/style1.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>webroot/css/timePicker.css" />

        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/init/views/scripts/jquery.min.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/init/views/scripts/jquery-ui-1.8.16.custom.min.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/init/views/scripts/jquery_history.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/init/views/scripts/jquery.crypt.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/init/views/scripts/jquery.form.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/init/views/scripts/info_box.js"></script>


        <!-- ESSE SCRIPT TÁ DANDO PROBLEMA
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/jquery-1.4.2.min.js"></script>
        -->

        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/topology/views/scripts/devices.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/topology/views/scripts/networks.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/topology/views/scripts/urns.js"></script>

        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/aaa/views/scripts/password.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/aaa/views/scripts/select.js"></script>

        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/jquery-ui.min.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/googlemaps.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/markerClusterer.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/StyledMarker.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/map.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/reservations.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/reservations_add.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/flows.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/timers.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/circuits/views/scripts/jquery.timePicker.js"></script>

	<script type ="text/javascript" language="JavaScript1.2" src="<?php echo $base; ?>apps/init/views/scripts/jquery.pjax.js"></script>

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
        echo '<script type ="text/javascript" language="JavaScript1.2" src="'.Dispatcher::getInstance()->url('').$f.'"></script>';
    } ?>
<?php endif; ?>	

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
                    <img src="<?php echo $base; ?>webroot/img/ajax-loader.gif" alt="<?php echo _('Loading'); ?>"/>
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
