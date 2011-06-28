<?php $args = $this->passedArgs ?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo Framework::getSystemName(); ?></title>

        <!-- GLOBAL JS SCRIPTS AND IN-LINE FUNCTIONS -->
        <link rel="stylesheet" type="text/css" href="layouts/style1.css" />
        
        <script type ="text/javascript" language="JavaScript1.2" src="apps/init/views/scripts/jquery.min.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/init/views/scripts/jquery-ui-1.8.13.custom.min.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/init/views/scripts/jquery_history.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/init/views/scripts/jquery.crypt.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/init/views/scripts/jquery.form.js"></script>
        
        <!-- ESSE SCRIPT TÁ DANDO PROBLEMA
        <script type ="text/javascript" language="JavaScript1.2" src="apps/circuits/views/scripts/jquery-1.4.2.min.js"></script>
        -->
        
        <script type ="text/javascript" language="JavaScript1.2" src="apps/circuits/views/scripts/jquery-ui.min.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/circuits/views/scripts/googlemaps.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/circuits/views/scripts/markerClusterer.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/circuits/views/scripts/StyledMarker.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/circuits/views/scripts/map.js"></script>
        <script type ="text/javascript" language="JavaScript1.2" src="apps/circuits/views/scripts/reservations.js"></script>
        
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <script type ="text/javascript">

            // variavel para armazenar o ID quando a função setInterval() é usada
            // cada vez que um link é carregado, é feito um clear na variável, para não carregar em páginas erradas
            var js_function_interval = null;
            
            // variavel global para armazenar o retorno de uma função de validação de um formulario, testada dentro do delegate
            var js_submit_form = true;

            $(document).ready(function(){                

                $("#info_box").load("main.php?app=init&controller=info_box");
                $("#menu").load("main.php?app=init&controller=menu");

                redir("main.php?<?php echo $args->last_view; ?>");

                $("body").delegate("a","click",function() {
                    if ($(this).attr("target") != "top") {
                        var content_show = $(this).attr("href");
                        if (content_show && (content_show[0] != "#")){
                            redir(content_show);
                        }
                    } else {
                        return true;
                    }
                    return false;
                }); //do delegate

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
                        redir(content_show, param);
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

            function loadHtml(htmlData) {
                clearInterval(js_function_interval);
                
                // carrega temporariamente a página para processá-la
                $('#htmlToLoad').html(htmlData);
                 $('#load_img').hide();
                // faz o redirecionamento das tags

                var flash = $('.flash_box').html();
                $('#flash_box').html(flash);

                var body = $('.content').html();
                $('#main').html(body);

                $.each($(".scripts i"), function() {
                    $.getScript($(this).html());
                });
                
                window.scroll(0, 0);
               
                // limpa a tag
                $('#htmlToLoad').empty();
            }

            function redir(url, param) {
                $('#main').empty();
                $('#load_img').show();
                
                //if (url) {
                    $.ajax ({
                        type: "POST",
                        url: url,
                        data: param,
                        success: loadHtml,
                        error: function(jqXHR) {
                            switch (jqXHR.status) {
                                case 401:
                                    top.location.href = 'index.php?message=<?php echo _("Not logged in"); ?>';
                                    break;
                                case 402:
                                    top.location.href = 'index.php?message=<?php echo _("Session Expired"); ?>';
                                    break;
                                case 404:
                                    $('#main').html("Page not found");
                                    break;
                                case 405:
                                    //change lang
                                    top.location.href = 'main.php?app=init&controller=gui';
                                    break;
                                case 406:
                                    //force refresh
                                    location.href = 'main.php?app=init&controller=gui';
                                    break;
                                default:
                                    $('#main').html("Unexpected error");
                            }
                        }
                     });
                 //}
            }

            function setFlash(message, status) {
                $('#flash_box').empty();
                if (!status)
                    status = "info";
                $('#flash_box').append('<div class="' + status + '">' + message + '</div>');
                window.scroll(0, 110);
            }
    
        </script>

    </head>

    <body>

        <!-- joga dentro dessa tag o html a ser processado - o que retorna do ajax -->
        <div id="htmlToLoad" style="display: none"></div>

        <div id="header" class="header">&nbsp;
            <div id="logo_box"> &nbsp;
                   <a href="<?php echo $this->buildLink(array('action' => 'welcome')); ?>"><img src="layouts/img/meican_white2.png" class="logo" alt="MEICAN"/></a>
            </div>
           
            <div id="info_box">

            </div>
        </div>
        <div id="content">
            <div id="menu">

            </div>
            <div id="flash_box">

            </div>
            <div id="load_img" style="display: none">
                    <img src="layouts/img/ajax-loader.gif" alt="<?php echo _('Loading'); ?>"/>
            </div>
            <div id="main">
                
            </div>
            </div>
        <div id="footer">
            <a href="#">
                        <?php echo _('About us'); ?>
            </a> |
            <a href="#">
                        <?php echo _('Developers'); ?>
            </a> |
            <a href="#">
                        <?php echo _('Terms of service'); ?>
            </a> |
            <a href="#">
                        <?php echo _('Privacy policy'); ?>
            </a>
            <br>
            2011        
        </div>
    </body>


</html>
