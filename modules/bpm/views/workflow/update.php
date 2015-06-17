<?php

use yii\helpers\Url;
use yii\helpers\Html;

use app\modules\bpm\assets\UpdateAsset;
UpdateAsset::register($this);

?>

<?= Html::csrfMetaTags() ?>

<script>
	var id = <?php echo json_encode($id); ?>;
</script>

<h1><?= Yii::t("bpm", 'Owner Domain:')." ".$domainName ?></h1>

<iframe style="width: 100%" name="workflow_editor" id="workflow_editor" src="<?php echo Yii::$app->urlManager->createUrl(['bpm/workflow/editor-update', 'id' => $_GET['id'], 'lang' => Yii::$app->language]);?>"></iframe>

<div class="controls">
    <input type="button" id="button_save" class="save" value=<?= Yii::t("bpm", "Save");?>>
    <input type="button" id="button_cancel" class="cancel" value=<?= Yii::t("bpm", "Cancel");?>>
</div>
