/**
 * Meican Alert 1.0
 *
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 * @author Mauricio Quatrin Guerreiro
 */

var MAlert = new MeicanAlert;

function MeicanAlert() {   
};

MeicanAlert.prototype.show = function(title, message, type, zIndex) {
    $.notify({
        icon: 'glyphicon glyphicon-ok-sign',
        title: '<strong>' + title + '</strong>',
        message: message
    },{
        type: type,
        delay: 10000,
        newest_on_top: true,
        offset: {
            y: 60,
            x: 20
        },
        z_index: 2000
    });
}