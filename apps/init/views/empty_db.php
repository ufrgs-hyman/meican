<?php if (!empty($title)): ?>
<h1><?php echo $title; ?></h1>
<?php endif; ?>

<?php if (!empty($message)): ?>
<h4><?php echo $message; ?></h4>
<?php endif; ?>

<?php if (!empty($link)): ?>
<div class="controls">
    <input class="add_new" type="button" value="<?php echo _('Add'); ?>" onclick="redir('<?php echo $this->url($link); ?>');"/>
</div>
<?php endif; ?>