<?php use yii\helpers\Html; ?>

<script>
	window.timeZone = "<?= Yii::$app->formatter->timeZone; ?>";
	var owner_domains = <?php echo json_encode($owner_domain); ?>;
    var domains = <?php echo json_encode($domains); ?>;
    var users = <?php echo json_encode($users); ?>;
    var admins = <?php echo json_encode($admins); ?>;
    var groups = <?php echo json_encode($groups); ?>;
    var devices = <?php echo json_encode($devices); ?>;
    var language = '<?= $_GET['lang']; ?>';
</script>

<script type="text/javascript" src="../../js/bpm/workflow/bpm-i18n.js"></script>
<script type="text/javascript" src="../../js/bpm/workflow/moment.js"></script>
<script type="text/javascript" src="../../js/bpm/workflow/moment-timezone.js"></script>

<script src="../../js/jquery.js"></script>
<script src="../../js/jquery-ui.js"></script>
<script src="../../js/jquery-ui.min.js"></script>

<!-- YUI CSS -->
<link rel="stylesheet" type="text/css" href="../../WireIt-0.5.0/lib/inputex/lib/yui/reset-fonts-grids/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" href="../../WireIt-0.5.0/lib/inputex/lib/yui/assets/skins/sam/skin.css" />

<!-- InputEx CSS -->
<link rel='stylesheet' type='text/css' href='../../WireIt-0.5.0/lib/inputex/css/inputEx.css' />

<!-- YUI-accordion CSS -->
<link rel="stylesheet" type="text/css" href="../../WireIt-0.5.0/lib/accordionview/assets/skins/sam/accordionview.css" />

<!-- WireIt CSS -->
<link rel="stylesheet" type="text/css" href="../../WireIt-0.5.0/css/WireIt.css" />
<link rel="stylesheet" type="text/css" href="../../WireIt-0.5.0/css/WireItEditor.css" />

<!-- Meican Workflow CSS -->
<link rel="stylesheet" type="text/css" href="../../css/workflow/workflowViewer.css" />

<!-- YUI -->
<script type="text/javascript" src="../../WireIt-0.5.0/lib/inputex/lib/yui/utilities/utilities.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/lib/inputex/lib/yui/resize/resize-min.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/lib/inputex/lib/yui/layout/layout-min.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/lib/inputex/lib/yui/container/container-min.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/lib/inputex/lib/yui/json/json-min.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/lib/inputex/lib/yui/button/button-min.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/lib/inputex/lib/yui/tabview/tabview-min.js"></script>

<!-- InputEx with wirable options -->
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/inputex.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/Field.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/util/inputex/WirableField-beta.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/Group.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/Visus.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/StringField.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/Textarea.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/SelectField.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/EmailField.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/UrlField.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/ListField.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/CheckBox.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/InPlaceEdit.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/CombineField.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/TimeField.js"></script>
<script type='text/javascript' src="../../WireIt-0.5.0/lib/inputex/js/fields/IntegerField.js"></script>

<!-- YUI-Accordion -->
<script type='text/javascript' src="../../WireIt-0.5.0/lib/accordionview/accordionview-min.js"></script>

<!-- WireIt -->
<!--[if IE]><script type="text/javascript" src="../../../../lib/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="../../WireIt-0.5.0/js/WireIt.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/CanvasElement.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/Wire.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/Terminal.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/util/DD.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/util/DDResize.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/Container.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/Layer.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/util/inputex/FormContainer-beta.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/LayerMap.js"></script>

<script type="text/javascript" src="../../WireIt-0.5.0/js/WiringEditorViewer.js"></script>

<script type="text/javascript" src="../../WireIt-0.5.0/js/ImageContainer.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/InOutContainer.js"></script>
<script type="text/javascript" src="../../WireIt-0.5.0/js/adapters/json-rpc.js"></script>

<script type="text/javascript" src="../../js/bpm/workflow/MeicanContainer.js"></script>
<script type="text/javascript" src="../../js/bpm/workflow/workflowLanguageViewer.js"></script>

<script>
	// InputEx needs a correct path to this image
	inputEx.spacerUrl = "/inputex/trunk/images/space.gif";
	var editor
	
	YAHOO.util.Event.onDOMReady( function() {
		editor = new WireIt.WiringEditor(workflowLanguageViewer);
	
		// Open the infos panel
		editor.accordionView.openPanel(1);

	});
</script>

<body>

	<div id="top">
	    <div id="propertiesForm"></div>
    </div>

    <div  id="center" class="worflow-editor-area">
    <!-- style="pointer-events: none;" -->
    	
    </div>

    <div id="button">
    </div>

</body>