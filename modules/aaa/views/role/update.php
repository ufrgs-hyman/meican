<?php
	use app\modules\aaa\assets\RoleCreateEditAsset;
	
	RoleCreateEditAsset::register($this);
?>

<script>
    var systemGroups = '<?= json_encode($systemGroups) ?>';
</script>

<h1><?= Yii::t("aaa", 'Edit role');?></h1>

<?= $this->render('_form', array('udr' => $udr, 'domains' => $domains, 'groups' => $groups, 'anyDomain' => $anyDomain)); ?>