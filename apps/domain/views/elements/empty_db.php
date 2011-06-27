<?php

$title = isset($argsToElement->title) ? $argsToElement->title : NULL;
$message = isset($argsToElement->message) ? $argsToElement->message : NULL;
$link = isset($argsToElement->link) ? $this->buildLink($argsToElement->link) : $this->buildLink(array('action' => 'add_form'));

?>

<?php if ($title): ?>
<h1><?php echo $title; ?></h1>
<?php endif; ?>

<?php if ($message): ?>
<h3><?php echo $message; ?></h3>
<?php endif; ?>

<br/>

<div class="controls">
    <input class="add_new" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $link; ?>');"/>
</div>