<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\helpers\Url;
use yii\helpers\Html;

use meican\bpm\assets\ViewerAsset;
ViewerAsset::register($this);

$this->params['header'] = [Yii::t("bpm", 'Viewer'), ['Home', 'Workflows']];

?>

<?= Html::csrfMetaTags() ?>

<script>
	var id = <?php echo json_encode($id); ?>;
</script>

<div class="box box-default">

    <h5 style="margin-bottom: 0px; margin-left: 10px;"><?= Yii::t("bpm", 'Owner Domain:')." ".$domainName ?></h5>
	<h4 style="margin-bottom: 0px; margin-left: 10px;"><?= Yii::t("bpm", 'Workflow Name:')." ".$workName ?></h4>

    <div  id="frame" class="box-body">  
		<iframe class="embed-responsive-item" style="width: 100%; height: 650px; border: none;" name="workflow_editor" id="workflow_editor" src="<?php echo Yii::$app->urlManager->createUrl(['bpm/workflow/editor-viewer', 'id' => $_GET['id'], 'lang' => Yii::$app->language]);?>"></iframe>
	</div>
	
	<div class="box-footer with-border">
        <?php if($status) echo '<input type="button" id="button_edit" disabled class="btn btn-primary" value='.Yii::t("bpm", 'Update').'>';
        	  else echo '<input type="button" id="button_edit" class="btn btn-primary" onclick ="update('.$id.')" value='.Yii::t("bpm", 'Update').'>';
    	?>
    </div>
</div>