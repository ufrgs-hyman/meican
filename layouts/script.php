<?php if ($this->jsFiles): ?>
<div class="scripts">
    <?php foreach ($this->jsFiles as $f) {
        echo "<i>".Dispatcher::getInstance()->url('/').$f."</i>";
    } ?>
</div>
<?php endif; ?>

<script>
<?php

if ($this->scriptArgs) {
    foreach ($this->scriptArgs as $name => $val) {
        if (is_string($val))
            echo "var $name = '$val';";
        elseif (is_array($val) || is_object($val))
            echo "var $name = ".json_encode($val).";";
        else
            echo "var $name = $val;";
    }
}

?>
</script>