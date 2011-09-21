<?php $icons = $this->passedArgs; ?>

<br/>

<?php if ($icons): ?>

    <table class="icons">

        <?php for ($ind = 0; $ind < count($icons); $ind = $ind + 2): ?>
            <tr>
                <td>
                    <?php echo $icons[$ind]->name; ?>
                </td>
                <td>
                    <?php if ($icons[$ind + 1])
                        echo $icons[$ind + 1]->name; ?>
                </td>
            </tr>

            <tr class="new_line">
                <td>
                    <a href="<?php echo $this->buildLink($icons[$ind]->link); ?>">
                        <img src="<?php echo $this->url($icons[$ind]->figure); ?>" alt="<?php echo $icons[$ind]->name; ?>"/>
                    </a>
                </td>
                <td>
                    <?php if ($icons[$ind + 1]): ?>
                        <a href="<?php echo $this->buildLink($icons[$ind + 1]->link); ?>">
                            <img src="<?php echo $this->url($icons[$ind + 1]->figure); ?>" alt="<?php echo $icons[$ind + 1]->name; ?>"/>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
    <?php endfor; ?>

    </table>

<?php endif; ?>
