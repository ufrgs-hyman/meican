<?php

use yii\helpers\Url;
use yii\helpers\Html;

use app\modules\bpm\assets\ViewerAsset;
ViewerAsset::register($this);

?>

<?= Html::csrfMetaTags() ?>

<script>
	var id = <?php echo json_encode($id); ?>;
</script>

<h1><?= Yii::t("bpm", 'Owner Domain:')." ".$domainName ?></h1>
<h1><?= Yii::t("bpm", 'Workflow Name:')." ".$workName ?></h1>

<div id="editor">
<iframe style="width: 100%" name="workflow_editor" id="workflow_editor" src="<?php echo Yii::$app->urlManager->createUrl(['bpm/workflow/editor-viewer', 'id' => $_GET['id'], 'lang' => Yii::$app->language]);?>"></iframe>
</div>

<div class="controls">
    <input type="button" id="button_cancel" class="cancel" value=<?= Yii::t("bpm", "Back");?>>
</div>
