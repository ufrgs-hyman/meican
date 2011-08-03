<?php $user = $argsToElement; ?>

<h2><?php echo _('Information'); ?></h2>

<table>
    <tr>
        <th>
            <?php echo _('Name'); ?>
        </th>
        
        <td>
            <input type="text" size="50" name="usr_name" value="<?php if ($user) echo $user->usr_name; ?>">
        </td>
    </tr>
    <tr>
        <th>
            <?php echo _('E-mail'); ?>
        </th>
        <td>
            <input type="text" size="50" name="usr_email" value="<?php if ($user) echo $user->usr_email; ?>">
        </td>
    </tr>
</table>