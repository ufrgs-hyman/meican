<?php $icons = $this->passedArgs; ?>
<?php if (!empty($icons)): ?>
    <div class="dashboard">
        <?php foreach ($icons as $icon): ?>
            <div>
                <h1><?php echo $icon->name; ?></h1>
                <a href="<?php echo $this->buildLink($icon->link); ?>">
                    <img src="<?php echo $this->url($icon->figure); ?>" alt="<?php echo $icon->name; ?>"/>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>