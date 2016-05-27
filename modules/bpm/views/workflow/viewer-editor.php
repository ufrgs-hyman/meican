<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

\meican\bpm\assets\ViewerEditor::register($this);

?>

<div id="top">
    <div id="propertiesForm"></div>
</div>

<div  id="center" class="worflow-editor-area">
<!-- style="pointer-events: none;" -->
	
</div>

<div id="button">
</div>

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