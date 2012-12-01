<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        
        <link rel="icon" href="../favicon.ico" type="image/png" />
        <link rel="SHORTCUT ICON" href="../favicon.ico" type="image/png" />

        <!-- YUI -->
        <link rel="stylesheet" type="text/css" href="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/reset-fonts-grids/reset-fonts-grids.css" />
        <link rel="stylesheet" type="text/css" href="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/assets/skins/sam/skin.css" />

        <!-- InputEx CSS -->
        <link type='text/css' rel='stylesheet' href='<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/css/inputEx.css' />

        <!-- YUI-accordion CSS -->
        <link rel="stylesheet" type="text/css" href="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/accordionview/assets/skins/sam/accordionview.css" />

        <!-- WireIt CSS -->
        <link rel="stylesheet" type="text/css" href="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/css/WireIt.css" />
        <link rel="stylesheet" type="text/css" href="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/css/WireItEditor.css" />

        <style>
            div.WireIt-Container {
                width: 80px; /* Prevent the modules from scratching on the right */

            }


            div.WireIt-Container div.body{
                border: 0px;
            }

            div.WireIt-InputExTerminal {
                float: left;
                width: 21px;
                height: 21px;
                position: relative;
            }
            div.WireIt-InputExTerminal div.WireIt-Terminal {
                top: -3px;
                left: -7px;
            }
            div.inputEx-Group div.inputEx-label div.inputEx-Field{
                width:80px;
            }

            div.Bubble div.body {
                width: 70px;
                height: 45px;
                opacity: 0.8;
                cursor: move;
            }

            .WiringEditor-module span {
                position: relative;
                top: -3px;
                display: none;
            }

            div.WireIt-Container-closebutton {
                /*background-image: url(../images/close.png);*/
                width: 25px;
                height: 15px;
                position: absolute;
                top: -6px;
                right:4px;
                cursor: pointer;
            }

            .inputEx-InPlaceEdit-visu{
                width: 70px;
                text-align: center;
            }

            .WiringEditor-module-User .body, .WiringEditor-module-Bandwidth .body, .WiringEditor-module-New_Request .body, .WiringEditor-module-Domain .body, .WiringEditor-module-Deny_Automatically .body, .WiringEditor-module-Accept_Automatically .body{
                width: 60px;
                height: 60px;
            }

            .WiringEditor-module-Request_Group_Authorization .body, .WiringEditor-module-Request_User_Authorization .body{
                width: 84px;
                height: 60px;
            }

            .WiringEditor-module-Bandwidth input[type="text"] {
                width: 60px;
            }


            .WireIt-MeicanContainer  > .body > .inputEx-Group {
                position: absolute;
                top: 72px;
                left: 0px;
            }
         
            .worflow-editor-area {
                border: 1px solid black;
            }

        </style>


        <!-- YUI -->
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/utilities/utilities.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/resize/resize-min.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/layout/layout-min.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/container/container-min.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/json/json-min.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/button/button-min.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/yui/tabview/tabview-min.js"></script>

        <!-- InputEx with wirable options (WirableField-beta) -->
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/inputex.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/Field.js"  type='text/javascript'></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/util/inputex/WirableField-beta.js"></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/Group.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/Visus.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/StringField.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/Textarea.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/SelectField.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/EmailField.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/UrlField.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/ListField.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/CheckBox.js"  type='text/javascript'></script>
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/inputex/js/fields/InPlaceEdit.js"  type='text/javascript'></script>

        <!-- YUI-Accordion -->
        <script src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/accordionview/accordionview-min.js"  type='text/javascript'></script>

        <!-- WireIt -->
        <!--[if IE]><script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/lib/excanvas.js"></script><![endif]-->
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/WireIt.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/CanvasElement.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Wire.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Terminal.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/util/DD.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/util/DDResize.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Container.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/Layer.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/util/inputex/FormContainer-beta.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/LayerMap.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/WiringEditor.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/ImageContainer.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/WireIt-0.5.0/js/InOutContainer.js"></script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/js/MeicanContainer.js"></script>

        <script type ="text/javascript">
            var baseUrl = '<?php echo $this->url(''); ?>';
            var workflow_teste = 'Teste 2';
        </script>
        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/js/json-rpc.js"></script>

        <script type="text/javascript" src="<?= $this->url() ?>apps/bpm/webroot/js/policyEditor.js"></script>

        <style>
            /* Comment Module */
            div.WireIt-Container.WiringEditor-module-comment { width: 200px; }
            div.WireIt-Container.WiringEditor-module-comment div.body { background-color: #EEEE66; }
            div.WireIt-Container.WiringEditor-module-comment div.body textarea { background-color: transparent; font-weight: bold; border: 0; }
        </style>


        <script>

            // InputEx needs a correct path to this image
            inputEx.spacerUrl = "/inputex/trunk/images/space.gif";


            YAHOO.util.Event.onDOMReady( function() {
                var editor = new WireIt.WiringEditor(meicanPolicyLanguage); 
	
                // Open the infos panel
                editor.accordionView.openPanel(2);
            });

        </script>

    </head>
    
    <h1><?= _("Workflow Name:") ?></h1>
    <body>
        
        <div id="top">
            <div id="toolbar"></div>
            <div id="propertiesForm"></div>
        </div>
            
        <div id="left">
        </div>
            
        <div id="center" class="worflow-editor-area">
        </div>
            
        <div id="helpPanel">
            <div class="hd">Welcome to the MEICAN Policy Editor</div>
        </div>
            
    </body>

</html>