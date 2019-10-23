<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\bpm\assets;

use yii\web\AssetBundle;

/**
 * @author Mauricio Quatrin Guerreiro
 */
class WireIt extends AssetBundle
{
    public $sourcePath = '@meican/bpm/assets/wireit';
    
    public $css = [
        //<!-- YUI CSS -->
        'lib/inputex/lib/yui/reset-fonts-grids/reset-fonts-grids.css',
        'lib/inputex/lib/yui/assets/skins/sam/skin.css',
        //<!-- InputEx CSS -->
        'lib/inputex/css/inputEx.css',
        //<!-- YUI-accordion CSS -->
        'lib/accordionview/assets/skins/sam/accordionview.css',
        //<!-- WireIt CSS -->
        'css/WireIt.css',
        'css/WireItEditor.css',
    ];

    public $js = [
        //jquery 171
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
        'bpm-i18n.js',
        //<!-- YUI -->
        'lib/inputex/lib/yui/utilities/utilities.js',
        'lib/inputex/lib/yui/resize/resize-min.js',
        'lib/inputex/lib/yui/layout/layout-min.js',
        'lib/inputex/lib/yui/container/container-min.js',
        'lib/inputex/lib/yui/json/json-min.js',
        'lib/inputex/lib/yui/button/button-min.js',
        'lib/inputex/lib/yui/tabview/tabview-min.js',
        //<!-- InputEx with wirable options -->
        'lib/inputex/js/inputex.js',
        'lib/inputex/js/Field.js',
        'js/util/inputex/WirableField-beta.js',
        'lib/inputex/js/Group.js',
        'lib/inputex/js/Visus.js',
        'lib/inputex/js/fields/StringField.js',
        'lib/inputex/js/fields/Textarea.js',
        'lib/inputex/js/fields/SelectField.js',
        'lib/inputex/js/fields/EmailField.js',
        'lib/inputex/js/fields/UrlField.js',
        'lib/inputex/js/fields/ListField.js',
        'lib/inputex/js/fields/CheckBox.js',
        'lib/inputex/js/fields/InPlaceEdit.js',
        'lib/inputex/js/fields/HiddenField.js',
        'lib/inputex/js/fields/CombineField.js',
        'lib/inputex/js/fields/TimeField.js',
        'lib/inputex/js/fields/IntegerField.js',
        //<!-- YUI-Accordion -->
        'lib/accordionview/accordionview-min.js',
        //<!-- WireIt -->
        'js/WireIt.js',
        'js/CanvasElement.js',
        'js/Wire.js',
        'js/Terminal.js',
        'js/util/DD.js',
        'js/util/DDResize.js',
        'js/Container.js',
        'js/Layer.js',
        'js/util/inputex/FormContainer-beta.js',
        'js/LayerMap.js',
        'js/WiringEditor.js',
        'js/ImageContainer.js',
        'js/InOutContainer.js',
        'js/adapters/json-rpc.js',
        'space.js'
    ];

    public $depends = [
    ];
}

