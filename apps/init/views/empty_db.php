<?php if (!emtpy($title)): ?>
<h1><?php echo $title; ?></h1>
<?php endif; ?>

<?php if (!empty($message)): ?>
<h3><?php echo $message; ?></h3>
<?php endif; ?>

<br/>

<div class="controls">
    <input class="add_new" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo @$link; ?>');"/>
</div>