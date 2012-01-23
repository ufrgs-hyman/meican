function updateSystemTime() {
    $.post(baseUrl+"init/info_box/time", function(data) {
        if (data)
            $("#system_date").html(data);
    });
}

var errorFunc = function(jqXHR) {
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
    
    var menuHandler = jQuery.fn.menuHandler = {
        menus : $('#menu ul ul'),
        load: function(){
            for (var i=0; i<this.menus.length; i++){
                if (window.localStorage.getItem('submenu_'+i) == "true"){
                    this.openSubMenu(i);
                } else {
                    $(this.menus[i]).css('display', 'none');
                }
            }
            
        },
        save: function(){//window.localStorage.removeItem('submenu_'+0); window.localStorage.removeItem('submenu_'+1); window.localStorage.removeItem('submenu_'+2);
            for (var i=0; i<this.menus.length; i++){
                window.localStorage.setItem('submenu_'+i, ($(this.menus[i]).css('display') != 'none'));
            }
        },
        openSubMenu: function(i){
            if (typeof(i) != "object")
                i = this.menus[i];
            $(i).slideDown().parent().find('h3 span.ui-icon').removeClass('ui-icon-circle-arrow-e').addClass('ui-icon-circle-arrow-s');
        },
        closeSubMenu: function(i){
            if (typeof(i) != "object")
                i = this.menus[i];
            $(i).slideUp().parent().find('h3 span.ui-icon').addClass('ui-icon-circle-arrow-e').removeClass('ui-icon-circle-arrow-s');
        },
        toggleSubMenu : function(i){
            if (typeof(i) != "object")
                i = this.menus[i];
            if ($(i).css('display') == "none")
                this.openSubMenu(i);
            else
                this.closeSubMenu(i);
        },
        setSelected: function() {
            $('#menu .active').removeClass("active");
            var selectedMenu = $('#menu a[href="'+window.location.pathname+'"]').addClass("active");
            if (!selectedMenu.hasClass('top')){
                this.openSubMenu(selectedMenu.parent().parent());
            }
            setTimeout(function(){
                menuHandler.save();
            }, 500);
        },
        prepare: function() {
            
            this.load();
            $('#menu h3 a').click(function(){
                if (!$(this).attr('href')){
                    menuHandler.toggleSubMenu($(this).parent().next());
                    /*
                    if ($(this).parent().next().css('display') == 'none'){
                        $(this).find('span.ui-icon').addClass('ui-icon-circle-arrow-e').removeClass('ui-icon-circle-arrow-s');
                    } else {
                        $(this).find('span.ui-icon').removeClass('ui-icon-circle-arrow-e').addClass('ui-icon-circle-arrow-s');
                    }*/

                    setTimeout(function(){
                        menuHandler.save();
                    }, 500);
                    return false;
                }
            });
        }
    }
    
    jQuery.fn.uify = function() {
        $(this).find('button,input[type=submit],input[type=button]').button();
        $(this).find('button[disabled=disabled],input[disabled=disabled][type=submit],input[disabled=disabled][type=button]').button('disabled');
        
        //$('input[type=button].add').button({icon: 'plusthick'});
        //$('input[type=button].add').button({ icons: {primary:'ui-icon-plusthick',secondary:'ui-icon-plusthick'} });
        $(this).find('[disabled=disabled]').addClass('ui-state-disabled');
        $(this).find('input[type!=submit],textarea,select').addClass('ui-widget ui-widget-content');
        $(this).find('table.list').addClass('ui-widget ui-corner-all');
        $(this).find('fieldset').addClass('ui-widget ui-corner-all');
        $(this).find('table.list thead').addClass('ui-widget-header');
        $(this).find('table.list tbody').addClass('ui-widget-content');

    /*        $(this).find('div.menu').addClass('ui-widget');
        $(this).find('div.topItem').addClass('ui-widget-header');
        $(this).find('div.subItem').addClass('ui-widget-content');*/
    };
    

    $.feedbackTab.init();
    /* $("#info_box").load("<?php echo $this->url(array("app" => "init", "controller" => "info_box")); ?>", function() {
                    
                   
                });*/
    
    //$("#menu").load("<?php echo $this->url(array("app" => "init", "controller" => "menu"));  ?>");
    if (jQuery.isFunction(jQuery.fn.pjax)){           
        $('a[href][href!=""][href!="#"]').pjax('#main', {
            error: errorFunc, 
            timeout: 5000
        });
        $('#main')
        .bind('start.pjax', function() {
            /*clearFlash();
            $('#main').empty();*/
            $('#loading').show();

            clearInterval(js_function_interval);
        })
        .bind('end.pjax', function(xhr) {
        
            window.scroll(0, 0);
            //$('#main').hide();
            //clearInterval(js_function_interval);
                        
            $('#flash_box').html($('.flash_box').html());
            $('.flash_box').remove();
                        
            /*            $.each($(".scripts script"), function() {
                $.getScript($(this).attr('src'));
            });
            $('.scripts').remove();*/
            //$('#main').html($('.content').html());
            //$('#main').show();
			$(window).trigger('resize');
            $('#loading').hide();
        });

        $("body").delegate("form","submit",function() {
            if (!js_submit_form) {
                js_submit_form = true;
                return false;
            }

            var content_show = $(this).attr("action");
            var param = $(this).serialize();

            if (content_show && param)
                $.pjax({
                    type: "POST",
                    url: content_show,
                    data: param,
                    error: errorFunc,
                    container: '#main',
                    timeout: 7000
                });
            return false;
        });
    
    
        $('#main').bind('end.pjax', function(){
        
            $('#main').uify();
            $('body').uify();
            if (jQuery.isFunction(jQuery.fn.tablesorter))
                $("table.list").tablesorter(/*{cssAsc: 'ui-icon ui-icon-triangle-1-n', cssDesc: 'ui-icon ui-icon-triangle-1-s'}*/);
            if (jQuery.isFunction(jQuery.fn.dataTable))
                $("table.list").dataTable(/*{cssAsc: 'ui-icon ui-icon-triangle-1-n', cssDesc: 'ui-icon ui-icon-triangle-1-s'}*/);
            menuHandler.setSelected();
        
        });
        menuHandler.prepare();
        $('#main').trigger('end.pjax');
    }
// analisar a real necessidade disso
//setTimeout(refresh, 10*60*1000); // carrega a página a cada 10 min., para não sobrecarregar scripts
}); //do ready

function redir(url, data){
    $.pjax({
        type: "POST",
        url: url,
        data: data,
        error: errorFunc,
        container: '#main',
        timeout: 7000
    });
    return false;
}

function clearSelectBox(htmlId){
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
    $('#flash_box').append('<div class="' + status + ' ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-closethick close-button" onclick="clearFlash();"></span>'+ message +
        '</p>');
    window.scroll(0, 0);
    window.onscroll = $.windowScroll;
    //window.onscroll = window_scroll;
}

function clearFlash(){
    $('#flash_box').empty();
    window.onscroll = null;
}

function WPToggle(divId, imageId) {

    if ($(divId).css("display") == "none") {
        $(divId).slideDown();
        $(imageId).attr("src", baseUrl+"webroot/img/minus.gif" );
    }

    else {
        $(divId).slideUp();
        $(imageId).attr("src", baseUrl+"webroot/img/plus.gif");
    }

}
 
(function($){ 
    $.fn.formatFields = function(){
        var intInp = $(this).find('.integer-input[disabled!=true]'),
        curInp = $(this).find('.currency-input[disabled!=true]');
        if (intInp.length>0 || curInp.length>0){
            applySpinner = function(){
                intInp.numeric('.').spinner({});
                if (curInp.length>0)
                    if (window.Globalization)
                        curInp.numeric('.').spinner({
                            numberformat: 'n'
                        });
                    else
                        $.getScript(baseUrl+'webroot/js/jquery.global.js', function(){
                            window.Globalization = jQuery.global;
                            curInp.numeric('.').spinner({
                                numberformat: 'n'
                            });
                        });
            //Trigger change event in field when spinner changes
            /* $(this).find(".ui-spinner-button").bind("click", function() {
                    $('.ui-spinner-button').parent().find('.ui-spinner-input').trigger("change");
                });
                $(this).find(".ui-spinner").bind("mouseup", function() {
                    $(this).find('.ui-spinner-input').trigger("change");
                //alert(intInp.numeric('.').val());
                }).bind("keyup", function() {
                    $(this).find('.ui-spinner-input').trigger("change");
                //alert(intInp.numeric('.').val());
                });*/
            };
            if (jQuery.isFunction(jQuery.fn.spinner))
                applySpinner();
            else
                $.getScript(baseUrl+'webroot/js/ui.spinner.js', applySpinner);
        }
        return this;
    };
    
    $.fn.toggleDisabled = function(){
        return this.each(function(){
            this.disabled = !this.disabled;
            if (this.disabled)
                $(this).addClass('ui-state-disabled');
            else
                $(this).removeClass('ui-state-disabled');
        });
    };
    
    $.fn.disabled = function(value){
        if (value == null)
            value = true;
        return this.each(function(){
            this.disabled = value;
            if (value)
                $(this).addClass('ui-state-disabled');
            else
                $(this).removeClass('ui-state-disabled');
        });
    };
    
    $.extend({
        redir : function(url, data){
            return redir(baseUrl+url, data);
        /*$.pjax({
            type: "POST",
            url: url,
            data: data,
            error: errorFunc,
            container: '#main',
            timeout: 5000
        });
        return false;*/
        },
        
        windowScroll: function () {
            var top = $(window).scrollTop();
            if (top >= 110) {
                $("#flash_box").addClass("fixed");
            } else {
                $("#flash_box").removeClass("fixed");
            }
        },
        
        feedbackTab : {
 
            speed:300,
            containerWidth:$('.feedback-panel').outerWidth(),
            containerHeight: $('.feedback-panel').height(),//$('.feedback-panel').outerHeight(),
            tabWidth:$('a.feedback-link').outerWidth(),
	 
	 
            init:function(){
                //$('.feedback-panel').css('height',$.feedbackTab.containerHeight + 'px');
                $('.feedback-panel').css('top', '-' + ($('.feedback-panel').outerHeight()+70) + 'px');
	 
                $('.feedback-link').click(function(event){
                    if ($('.feedback-panel').hasClass('open')) {
                        $('.feedback-panel')
                        .animate({
                            top: '-' + ($('.feedback-panel').outerHeight()+70) + 'px'
                        }, $.feedbackTab.speed)
                        .removeClass('open');
                        $('#MainOverlay').hide();
                    } else {
                        $('.feedback-panel')
                        .animate({
                            top: $('a.feedback-link').offset().top + 15 + 'px'
                        },  $.feedbackTab.speed)
                        .addClass('open');
                        $('#MainOverlay').show();
                    }
                    event.preventDefault();
                });
                $('#feedback-tabs li').click(function (){
                    $('#feedback-tabs li').removeClass('active');
                    $(this).addClass('active');
                    $('#topic_style').val($(this).attr('class').split(' ')[0]);
                });
                $('#topic_additional_detail, #topic_subject').keyup(function(){
                    if ($(this).val().length > 0){
                        $(this).prev().css({
                            display: 'none'
                        });
                    } else {
                        $(this).prev().css({
                            display: 'block'
                        });
                    }
                });
                $('#feedback-tabs a').click(function (){
                    $('#feedback-tabs li.active').removeClass('active');
                    $(this).parent().addClass('active');
                    var type = $(this).parent().attr('class').split(' ')[0];
                    $('#topic_style').val(type);	
                    $('#topic_additional_detail_label').html(feedback_descrbs[type]);
                    return false;
                });
                $('#feedback-tabs li.idea a').click();
                $('#emotion_select a').click(function(){
                    $('#topic_emotitag_feeling').val($(this).attr('class'));
                    $('#emotion_select').toggle();
                    return false;
                });
                $('#emotion_selected').click(function(){
                    $('#emotion_select').toggle();
                    return false;
                });
                $('#feedback-panel form').submit(function(){
                    $.ajax({
                        type: 'post',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        success: function (data){
                            alert(data);
                            $('.feedback-panel')
                            .animate({
                                top: '-' + ($('.feedback-panel').outerHeight()+70) + 'px'
                            }, $.feedbackTab.speed)
                            .removeClass('open');
				
                        },
                        error: function (){
                            alert('Problems to send, try again later');
                        }
                    })
                    ;
                    return false;
                });
            }
        }
                
    });

})(jQuery);
