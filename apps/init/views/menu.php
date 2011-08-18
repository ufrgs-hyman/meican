<?php $menu = $this->passedArgs; ?>

<?php foreach ($menu->item as $i): ?>

    <div class="topItem">
        <?php if ($i->link): ?>
            <a href="<?php echo $i->link; ?>" target="main"><?php echo _($i->name); ?></a>
        <?php else: ?>
            <?php echo _($i->name); ?>
        <?php endif; ?>
    </div>
    
    <?php foreach ($i->subItem as $s): ?>
        <div class="subItem"><a href="<?php echo $s->link ?>" target="main"><?php echo _($s->name); ?></a></div>
    <?php endforeach; ?>

<?php endforeach; ?>