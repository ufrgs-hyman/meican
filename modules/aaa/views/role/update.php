<?php
	use meican\aaa\assets\role\CreateEditAsset;
	
	CreateEditAsset::register($this);
?>

<script>
    var systemGroups = '<?= json_encode($systemGroups) ?>';
</script>

<h1><?= Yii::t("aaa", 'Edit role');?></h1>

<?= $this->render('_form', array('udr' => $udr, 'domains' => $domains, 'groups' => $groups, 'anyDomain' => $anyDomain)); ?>