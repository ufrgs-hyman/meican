<?php

$title = isset($argsToElement->title) ? $argsToElement->title : NULL;
$message = isset($argsToElement->message) ? $argsToElement->message : NULL;
$import_link = isset($argsToElement->import_link) ? $this->buildLink($argsToElement->import_link) : $this->buildLink(array('action' => 'add_form'));
$add_link = $this->buildLink($argsToElement->add_link);

?>

<?php if ($title): ?>
<h1><?php echo $title; ?></h1>
<?php endif; ?>

<?php if ($message): ?>
<h4><?php echo $message; ?></h4>
<?php endif; ?>

<br/>

<div class="controls">
    <input class="add_new" type="button" style="float:right" value="<?php echo _('Populate from topology'); ?>" onclick="redir('<?php echo $import_link; ?>');"/>
    <input class="add_new" type="button" style="float:right" value="<?php echo _('Populate manual'); ?>" onclick="redir('<?php echo $add_link; ?>');"/>
</div>