<?php $args = $this->passedArgs ?>
<?php $base = Dispatcher::getInstance()->base.'/'; ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo Framework::getSystemName(); ?></title>

        <!-- GLOBAL JS SCRIPTS AND IN-LINE FUNCTIONS -->
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>layouts/style1.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $base; ?>layouts/timePicker.css" />

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
            var baseUrl = <?php echo $this->url(''); ?>

            $(document).ready(function() {

               /* $("#info_box").load("<?php echo $this->url(array("app" => "init", "controller" => "info_box")); ?>", function() {
                    
                   
                });*/
                 setInterval("updateSystemTime()", 60000);<?php // chamada para atualizar a hora?>
                //$("#menu").load("<?php echo $this->url(array("app" => "init", "controller" => "menu"));  ?>");
                var errorFunc = function(jqXHR) {
                        switch (jqXHR.status) {
                            case 401:
                                top.location.href = baseUrl+'<?php //index.php?message=<?php echo _("Not logged in"); ?>';
                                break;
                            case 402:
                                top.location.href = baseUrl+'<?php //index.php?message=<?php echo _("Session Expired"); ?>';
                                break;
                            case 404:
                                $('#main').html("Page not found");
                                break;
                            case 405:
                                //change lang
                                top.location.href = baseUrl+'init/gui';
                                break;
                            case 406:
                                //force refresh
                                location.href = baseUrl+'init/gui';
                                break;
                            default:
                                $('#main').html("Unexpected error");
                            }
                        };
                $('a').pjax('#main', {error: errorFunc});
                $('#main')
                  .bind('start.pjax', function() {
                    clearFlash();
                    $('#main').empty();
                    $('#load_img').show();

                    clearInterval(js_function_interval);
                  })
                  .bind('end.pjax',   function(xhr) {
                        clearInterval(js_function_interval);
                        
                        $('#flash_box').html($('.flash_box').html());
                        
                        $.each($(".scripts i"), function() {
                            $.getScript($(this).html());
                        });
                        $('#load_img').hide();
                        $('#main').html($('.content').html());

                        window.scroll(0, 0);

                  });

                $("body").delegate("form","submit",function() {
                    if (!js_submit_form) {
                        js_submit_form = true;
                        return false;
                    }

                    $.each($(':password'), function() {
                        if ($(this).val()) {
                            var md5 = $(this).crypt({method:"md5"});
                            $(this).attr({style: 'display: none'});
                            $(this).val(md5);
                        }
                    });

                    var content_show = $(this).attr("action");
                    var param = $('form').serialize();

                    if (content_show && param)
                        $.pjax({
                            type: "POST",
                            url: content_show,
                            data: param,
                            error: errorFunc,
                            container: '#main'
                        });
                    return false;
                });

                // analisar a real necessidade disso
                //setTimeout(refresh, 10*60*1000); // carrega a página a cada 10 min., para não sobrecarregar scripts

            }); //do ready

            function clearSelectBox(htmlId) {
                $(htmlId).empty();
                $(htmlId).append('<option value="-1"></option>');
            }

            function fillSelectBox(htmlId, fillerArray, current_val) {
                clearSelectBox(htmlId);
                for (var i=0; i < fillerArray.length; i++) {
                    if (fillerArray[i].id == current_val)
                        $(htmlId).append('<option selected="true" value="' + fillerArray[i].id + '">' + fillerArray[i].name + '</option>');
                    else
                        $(htmlId).append('<option value="' + fillerArray[i].id + '">' + fillerArray[i].name + '</option>');
                }
            }
            
            function setFlash(message, status) {
                $('#flash_box').empty();
                if (!status)
                    status = "info";
                $('#flash_box').append('<div class="' + status + '">' + message +
                    '<input type="button" class="closeFlash" onclick="clearFlash()"/>' +
                    '</div> ');
                window.scroll(0, 0);
                window.onscroll = window_scroll;
            }

            function clearFlash(){
                $('#flash_box').empty();
                window.onscroll = null;
            }

            function WPToggle(divId, imageId) {

                if ($(divId).css("display") == "none") {
                    $(divId).slideDown();
                    $(imageId).attr("src", baseUrl+"layouts/img/minus.gif" );
                }

                else {
                    $(divId).slideUp();
                    $(imageId).attr("src", baseUrl+"layouts/img/plus.gif");
                }

            }

            function window_scroll() {
                var top = $(window).scrollTop();
                if (top >= 110) {
                    $("#flash_box").addClass("fixed");
                } else {
                    $("#flash_box").removeClass("fixed");
                }
            }

        </script>

    </head>

    <body>

        <div id="auxDiv">
        </div>
        <!-- joga dentro dessa tag o html a ser processado - o que retorna do ajax -->
        <div id="htmlToLoad" style="display: none"></div>
            <div id="header" class="header">
                <div id="logo_box">
                    <a href="<?php echo $this->buildLink(array('action' => 'welcome')); ?>"><img class="logo" alt="MEICAN" src="<?php echo $this->url(''); ?>layouts/img/meican_white.png"/></a>
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
                    <img src="<?php echo $base; ?>layouts/img/ajax-loader.gif" alt="<?php echo _('Loading'); ?>"/>
                </div>
                <div id="main">
                    <?php echo $content_for_body; ?>
                </div>
            </div>
           <!-- <div id="footer">
                <img src="<?php echo $this->url(''); ?>layouts/img/footer.png" style="width:100%; position: absolute; height: 25px;"></img>
            </div>-->
    </body>


</html>