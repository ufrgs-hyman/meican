<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

\meican\bpm\assets\Editor::register($this);

?>

<h4>

	<div id="top">
	    <div id="propertiesForm"></div>
    </div>
     
    <div id="left">
    </div>
    
    <div id="center" class="worflow-editor-area">
    </div>
     
    <div id="right">
    </div>
    
    <div id="button">
    </div>

</h4>

<script>
    window.timeZone = "<?= Yii::$app->formatter->timeZone; ?>";
    var owner_domains = <?php echo json_encode($owner_domain); ?>;
    var domains = <?php echo json_encode($domains); ?>;
    var users = <?php echo json_encode($users); ?>;
    var admins = <?php echo json_encode($admins); ?>;
    var groups = <?php echo json_encode($groups); ?>;
    var language = '<?= $_GET['lang']; ?>';
</script>