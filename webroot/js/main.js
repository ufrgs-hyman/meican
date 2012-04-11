function updateSystemTime() {
    $.get(baseUrl+"init/info_box/time", function(data) {
        if (data)
            $("#system_date").html(data);
    });
}

$(function() {
    if (jQuery.isFunction(jQuery.fn.pjax)){           
        $('a[href][href!=""][href!="#"]').pjax("#main");
        $('#main')
        .bind('pjax:start', function() {
            $('#loading').show();
            clearInterval(js_function_interval);
        })
        .bind('pjax:end', function(xhr) {
            window.scroll(0, 0);
            $(window).trigger('resize');
            $('#loading').hide();
        });

        $("body").delegate("form","submit",function() {
            if (!js_submit_form) {
                js_submit_form = true;
                return false;
            }
            $.navigate({
                type: "POST",
                url: $(this).attr("action"),
                data: $(this).serialize()
            });
            return false;
        });
    
        $('#main').bind('pjax:end', function(){
        
            $('body').uify();
            if (jQuery.isFunction(jQuery.fn.tablesorter))
                $("table.list").tablesorter(/*{cssAsc: 'ui-icon ui-icon-triangle-1-n', cssDesc: 'ui-icon ui-icon-triangle-1-s'}*/);
            if (jQuery.isFunction(jQuery.fn.dataTable))
                $("table.list").dataTable(/*{cssAsc: 'ui-icon ui-icon-triangle-1-n', cssDesc: 'ui-icon ui-icon-triangle-1-s'}*/);
            $.fn.menuHandler.setSelected();
            $.makeAutofocus();
            $('input, textarea').placeholder();
            $(':checkbox').makeDeleteButton(':checkbox', '#DeleteButton');
        });
        $(window).bind('resize', function(){
            $('#menu').css('height', $(window).height()-$('#menu').offset().top-$('#system_date').height());
        });
        $('#main').trigger('pjax:end');
    }
    $.fn.menuHandler.prepare();
    $.feedbackTab.init();
});

function redir(url, data){
    $.redir(url, data);
}
            
function setFlash(message, status) {
    $('#flash_box').empty();
    $.flash(message, status);
    //window.onscroll = window_scroll;
}

function clearFlash(){
    $('#flash_box').empty();
    window.onscroll = null;
}
 
(function($){
    
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
        clear: function(){
            for (var i=0; i<this.menus.length; i++){
                window.localStorage.setItem('submenu_'+i, false);
            }
        },
        save: function(){//window.localStorage.removeItem('submenu_'+0); window.localStorage.removeItem('submenu_'+1); window.localStorage.removeItem('submenu_'+2);
            for (var i=0; i<this.menus.length; i++){
                window.localStorage.setItem('submenu_'+i, ($(this.menus[i]).css('display') != 'none'));
            }
        },
        openSubMenu: function(i, callback){
            if (typeof(i) != "object")
                i = this.menus[i];
            $(i).slideDown(400, callback).parent().find('h3 span.ui-icon').removeClass('ui-icon-circle-arrow-e').addClass('ui-icon-circle-arrow-s');
        },
        closeSubMenu: function(i, callback){
            if (typeof(i) != "object")
                i = this.menus[i];
            $(i).slideUp(400, callback).parent().find('h3 span.ui-icon').addClass('ui-icon-circle-arrow-e').removeClass('ui-icon-circle-arrow-s');
        },
        toggleSubMenu : function(i, callback ){
            if (typeof(i) != "object")
                i = this.menus[i];
            $(i).slideToggle(400, callback).parent().find('h3 span.ui-icon').toggleClass('ui-icon-circle-arrow-e').toggleClass('ui-icon-circle-arrow-s');/*
            if ($(i).css('display') == "none")
                this.openSubMenu(i);
            else
                this.closeSubMenu(i);*/
        },
        setSelected: function() {
            $('#menu .active').removeClass("active");
            var selectedMenu = $('#menu a[href="'+window.location.pathname+'"]').addClass("active");
            if (!selectedMenu.hasClass('top')){
                this.openSubMenu(selectedMenu.parent().parent(), function(){
                    menuHandler.save();
                });
            }
        },
        prepare: function() {
            this.menus = $('#menu ul ul');
            this.load();
            $('#menu h3 a').click(function(){
                if (!$(this).attr('href')){
                    menuHandler.toggleSubMenu($(this).parent().next(), function(){
                        menuHandler.save();
                    });
                    return false;
                }
            });
        }
    };
    
    jQuery.fn.uify = function() {
        //$(this).find('button,input[type=submit],input[type=button]').button();
        //$(this).find('button[disabled=disabled],input[disabled=disabled][type=submit],input[disabled=disabled][type=button]').button('disabled');
        
        //$('input[type=button].add').button({icon: 'plusthick'});
        //$('input[type=button].add').button({ icons: {primary:'ui-icon-plusthick',secondary:'ui-icon-plusthick'} });
        $(this).find('[disabled=disabled]').addClass('ui-state-disabled');
        /* $(this).find('input[type!=submit],textarea,select').addClass('ui-widget ui-widget-content');
        $(this).find('table.list').addClass('ui-widget ui-corner-all');
        $(this).find('fieldset').addClass('ui-widget ui-corner-all');
        $(this).find('table.list thead').addClass('ui-widget-hea2der');
        $(this).find('table.list tbody').addClass('ui-widget-content');*/

        /*        $(this).find('div.menu').addClass('ui-widget');
        $(this).find('div.topItem').addClass('ui-widget-header');
        $(this).find('div.subItem').addClass('ui-widget-content');*/
    };
    
    if (jQuery.isFunction(jQuery.fn.pjax)){  
        $.extend($.pjax.defaults, {
            error: function(jqXHR, textStatus) {
                switch (jqXHR.status) {
                    case 401:
                    case 402:
                    case 405://change lang
                    case 406://force refresh
                        top.location.href = baseUrl;
                        break;
                    case 0:
                    case 404:
                        setFlash("Page not found. Try to reload the current page.", 'error');
                        break;
                    default:
                        setFlash("Unexpected error "+jqXHR.status, 'error');
                }
                console.debug("Error on ajax: "+jqXHR.status+jqXHR.statusText);
                console.debug(jqXHR);
            },
            timeout: null
        });
    }
    
    
    $.fn.extend({
        makeDeleteButton: function(inpSelect, delSelector){
            var ev = function (){
                if ($(inpSelect+':checked').length >0)
                    $(delSelector).show();
                else
                    $(delSelector).hide();
            };
            $(this).bind('click change', ev);
            ev();
        },
        formatFields: function(){
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
                };
                if (jQuery.isFunction(jQuery.fn.spinner))
                    applySpinner();
                else
                    $.getScript(baseUrl+'webroot/js/ui.spinner.js', applySpinner);
            }
            return this;
        },
        toggleDisabled : function(){
            return this.each(function(){
                this.disabled = !this.disabled;
                if (this.disabled)
                    $(this).addClass('ui-state-disabled');
                else
                    $(this).removeClass('ui-state-disabled');
            });
        },
        disabled : function(value){
            if (value == null)
                value = true;
            return this.each(function(){
                this.disabled = value;
                if (value)
                    $(this).addClass('ui-state-disabled');
                else
                    $(this).removeClass('ui-state-disabled');
            });
        },
        clearSelectBox : function (){
            return this.empty().append('<option value="-1"></option>');
        },
        fillSelectBox : function (fillerArray, current_val, check_allow) {
            this.clearSelectBox();
            for (var i in fillerArray) {
                if ((check_allow) && (fillerArray[i].allow_create == false)) {
                    continue;       // if permission is needed and the user doesn't have, then doesn't fill the selectBox
                } else {            // if permission isn't needed or the user have permission, fill the selectBox'
                    if (fillerArray[i].id == current_val) 
                        this.append('<option selected="true" value="' + fillerArray[i].id + '">' + fillerArray[i].name + '</option>');
                    else 
                        this.append('<option value="' + fillerArray[i].id + '">' + fillerArray[i].name + '</option>');
                }
            }
            return this;
        }
    });
    
    $.extend({
        makeAutofocus: function(){
            if(!('autofocus' in document.createElement('input'))){
                $('input[autofocus]').eq(0).focus();
            }else{
                // Fix for opera
                $('input[autofocus]').eq(0).val('');    
                $('input[autofocus]').eq(0).removeClass('placeholder');
            }
        },
        
        flash: function (message, status){
            if (!status)
                status = "info";
            $('#flash_box').append('<div class="' + status + ' ui-corner-all shadow" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-closethick close-button" onclick="clearFlash();"></span>'+ message +
                '</p>');
            window.scroll(0, 0);
            window.onscroll = $.windowScroll;
        },
        
        redir : function(url, data){
            if (url[0]!='/')
                url = baseUrl+url;
            if (!data){
                $.navigate({
                    url: url
                });
            } else {
                $.navigate({
                    type: "POST",
                    url: url,
                    data: data
                });
            }
            return false;
        },
        
        navigate: function(obj){
            var data = $.extend({
                container: "#main"
            }, obj);
            return $.pjax(data);            
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
	 
                $('.feedback-link,#MainOverlay').click(function(e){
                    $('.feedback-panel').css('top', $('a.feedback-link').offset().top + 15 + 'px');
                    if ($('.feedback-panel').hasClass('open')) {
                        $('.feedback-panel').slideUp(this.speed).removeClass('open');
                        $('#MainOverlay').hide();
                    } else {
                        $('.feedback-panel').slideDown(this.speed).addClass('open');
                        $('#MainOverlay').show();
                    }
                    e.preventDefault();
                });
                $('#feedback-tabs li').click(function (){
                    $('#feedback-tabs li').removeClass('active');
                    $(this).addClass('active');
                    $('#topic_style').val($(this).attr('class').split(' ')[0]);
                });
                $('#feedback-tabs a').click(function (){
                    $('#feedback-tabs li.active').removeClass('active');
                    $(this).parent().addClass('active');
                    var type = $(this).parent().attr('class').split(' ')[0];
                    $('#topic_style').val(type);	
                    $('#topic_additional_detail').attr('placeholder', feedback_descrbs[type]);
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
                        type: 'POST',
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

/*! http://mths.be/placeholder v1.8.7 by @mathias */
(function(f,h,c){
    var a='placeholder' in h.createElement('input'),d='placeholder' in h.createElement('textarea'),i=c.fn,j;
    if(a&&d){
        j=i.placeholder=function(){
            return this
        };
            
        j.input=j.textarea=true
    }else{
        j=i.placeholder=function(){
            return this.filter((a?'textarea':':input')+'[placeholder]').not('.placeholder').bind('focus.placeholder',b).bind('blur.placeholder',e).trigger('blur.placeholder').end()
        };
            
        j.input=a;
        j.textarea=d;
        c(function(){
            c(h).delegate('form','submit.placeholder',function(){
                var k=c('.placeholder',this).each(b);
                setTimeout(function(){
                    k.each(e)
                },10)
            })
        });
        c(f).bind('unload.placeholder',function(){
            c('.placeholder').val('')
        })
    }
    function g(l){
        var k={},m=/^jQuery\d+$/;
        c.each(l.attributes,function(o,n){
            if(n.specified&&!m.test(n.name)){
                k[n.name]=n.value
            }
        });
        return k
    }
    function b(){
        var k=c(this);
        if(k.val()===k.attr('placeholder')&&k.hasClass('placeholder')){
            if(k.data('placeholder-password')){
                k.hide().next().show().focus().attr('id',k.removeAttr('id').data('placeholder-id'))
            }else{
                k.val('').removeClass('placeholder')
            }
        }
    }
    function e(){
        var o,n=c(this),k=n,m=this.id;
        if(n.val()===''){
            if(n.is(':password')){
                if(!n.data('placeholder-textinput')){
                    try{
                        o=n.clone().attr({
                            type:'text'
                        })
                    }catch(l){
                        o=c('<input>').attr(c.extend(g(this),{
                            type:'text'
                        }))
                    }
                    o.removeAttr('name').data('placeholder-password',true).data('placeholder-id',m).bind('focus.placeholder',b);
                    n.data('placeholder-textinput',o).data('placeholder-id',m).before(o)
                }
                n=n.removeAttr('id').hide().prev().attr('id',m).show()
            }
            n.addClass('placeholder').val(n.attr('placeholder'))
        }else{
            n.removeClass('placeholder')
        }
    }
}(this,document,jQuery));