<?php //<!-- LAYOUT DEFAULT --> ?>
<?php if ($this->script->scriptArgs): ?>
<script>
<?php
    foreach ($this->script->scriptArgs as $name => $val) {
        if (is_string($val))
            echo "var $name = '$val';\n";
        elseif (is_array($val) || is_object($val))
            echo "var $name = ".json_encode($val).";\n";
        else
            echo "var $name = $val;\n";
    }?>
</script>
<?php endif; ?>
<?php 
	if (!isset($scripts_for_layout))
		$scripts_for_layout = array();
	if (!empty($this->script->jsFiles))
		$scripts_for_layout += $this->script->jsFiles;
if (!empty($scripts_for_layout)): ?>
<div class="scripts">    
<?php 
	foreach ($scripts_for_layout as $script): ?>
	<script type="text/javascript" src="<?php echo Dispatcher::getInstance()->url('').$script ?>"></script>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($content_for_flash)): ?>
    <div class="flash_box">
    <?php echo $this->element('flash_box', array('app' => 'init')+compact('content_for_flash')); ?>
    </div>
<?php endif; ?>
<?php echo $content_for_body; ?>
