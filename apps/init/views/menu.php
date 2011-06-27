<?php $menu = $this->passedArgs; ?>

<?php foreach($menu->item as $i): ?>

<div class="topItem">
<?php echo _($i->name); ?>
            <?php foreach($i->subItem as $s): ?>
                <div class="subItem"><a href="<?= $s->link ?>" target="main"><?php echo _($s->name); ?></a></div>
            <?php endforeach; ?>
</div>

<?php endforeach; ?>