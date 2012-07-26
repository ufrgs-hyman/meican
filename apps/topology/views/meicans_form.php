<?php
$meican_descr = isset($meican->meican_descr) ? $meican->meican_descr : NULL;
$meican_ip = isset($meican->meican_ip) ? $meican->meican_ip : NULL;
$meican_dir_name = isset($meican->meican_dir_name) ? $meican->meican_dir_name : NULL;
$is_local_domain = isset($meican->local_domain) ? $meican->local_domain : FALSE;

if (empty($meican)){
    $title = _("Add %s");
    $link = array("action" => "add");
} else {
    $title = _("Edit %s");
    $link = array("action" => "update", "param" => "meican_id:$meican->meican_id");
}
?>

<h1><?php echo sprintf($title, _("MEICAN")); ?></h1>

<form method="POST" action="<?php echo $this->url($link); ?>">
        <div class="form input">
        <label for="meican_descr"><?php echo _("Name"); ?></label>
        <input type="text" name="meican_descr" size="30" value="<?php echo $meican_descr; ?>"/>
    </div>
    <div class="form input">
        <label for="meican_ip"><?php echo _("MEICAN IP"); ?></label>
        <input type="text" name="meican_ip" size="30" value="<?php echo $meican_ip; ?>"/>
    </div>
    <div class="form input">
        <label for="meican_dir_name"><?php echo _("Directory name"); ?></label>
        <input type="text" name="meican_dir_name" size="30" value="<?php echo $meican_dir_name; ?>"/>
    </div>
    <div class="form input">
        <label for="local_domain"><?php echo _("Is local domain?"); ?></label>
        <input type="checkbox" name="local_domain" <?php if ($is_local_domain) echo 'checked="true"'; ?>/>
    </div>
    
    
    <div class="controls">
        <input class="save" type="submit" value="<?php echo _('Submit'); ?>">
        <input class="cancel" type="button" value="<?php echo _('Cancel'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'show')); ?>');">
    </div>

    
</form>

