<div id="menu">
    <ul>
        <?php foreach (MenuItem::getAllMenus() as $menu): ?>
	        <li>
                <h3>
                    <?php if (!empty($menu->url)): ?>
                        <a href="<?php echo $this->url($menu->url); ?>" class="top"><?php echo $menu->label; ?></a>
                    <?php else: ?>
                        <a href="" class="top">
                    <span class="ui-icon ui-icon-circle-arrow-e"></span><?php echo $menu->label; ?></a>
                    <?php endif; ?>
                </h3>
            <?php if (!empty($menu->sub)): ?>
                <ul>
                    <?php foreach ($menu->sub as $subMenu): ?>
                        <li><a href="<?php echo $this->url($subMenu->url); ?>"><?php echo $subMenu->label; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
                </li>
        <?php endforeach; ?>
    </ul>
</div>
