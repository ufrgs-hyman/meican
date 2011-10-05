
            var errorFunc = function(jqXHR) {
                        setFlash("Poooooo", 'error');
                        switch (jqXHR.status) {
                            case 401:
                                top.location.href = baseUrl;
                                break;
                            case 402:
                                top.location.href = baseUrl;
                                break;
                            case 404:
                                setFlash("Page not found", 'error');
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
                                setFlash("Unexpected error "+jqXHR.status, 'error');
                            }
                        };

            $(document).ready(function() {

               /* $("#info_box").load("<?php echo $this->url(array("app" => "init", "controller" => "info_box")); ?>", function() {
                    
                   
                });*/
                 setInterval("updateSystemTime()", 60000);//<?php // chamada para atualizar a hora?>
                //$("#menu").load("<?php echo $this->url(array("app" => "init", "controller" => "menu"));  ?>");
                
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

            function redir(url, data){
                $.pjax({
                            type: "POST",
                            url: url,
                            data: data,
                            error: errorFunc,
                            container: '#main'
                        });
                return false;
            }

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
