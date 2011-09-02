<?php $icons = $this->passedArgs; ?>
<br/>
<table class="icons">
<?php
$ind = 0;
        while ($ind < count($icons)) :

     ?>

    <tr>
        <td>
            <?php echo $icons[$ind]->name; ?>
        </td>
        <td>
            <?php echo $icons[$ind+1]->name; ?>
        </td>
    </tr>
    <tr class="new_line">
        <td>
            <a href="<?php echo $this->buildLink($icons[$ind]->link); ?>">
                <img src="<?php echo $icons[$ind]->figure; ?>" alt="<?php echo $icons[0]->name; ?>">
            </a>
        </td>
        <td>
            <a href="<?php echo $this->buildLink($icons[$ind+1]->link); ?>">
                <img src="<?php echo $icons[$ind+1]->figure; ?>" alt="<?php echo $icons[1]->name; ?>">
            </a>
        </td>
    </tr>
    <?php $ind = $ind + 2;
    endwhile; ?>
</table>