<?php
    $domains = $this->passedArgs;

?>

<form action="<?php echo $this->buildLink(array('action' => 'saveRequest')); ?>" method='POST'>

    <table>

        <tr>
            <td>
                <label for='domainDst'><?php echo _('Destination Domain'); ?></label>
            </td>
            <td>
                <select name='domainDst' onChange='updateUsers(this,"userDst")' >
                    <option selected="true" value="-1"></option>
                    <?php foreach ($domains as $ind => $val) : ?>
                        <option value="<?php echo $val->dom_ip; ?>"><?php echo $val->dom_descr; ?></option>
                    <?php endforeach; ?>

                </select>
            </td>
        </tr>

        <tr>
            <td>
                <label for='userDst'><?php echo _('Destination User'); ?></label>
            </td>
            <td>
                <select name='userDst' id='userDst' disabled="disabled" style="display: none">
                    <option selected="true" value="-1"></option>
                    <?php foreach ($users as $ind => $val) : ?>
                        <option value="<?php echo $val->usr_id; ?>"><?php echo $val->usr_name; ?></option>
                    <?php endforeach; ?>

                </select>
            </td>
        </tr>

        <tr>
            <td>
                <label for='question'><?php echo _('Question'); ?></label>
            </td>
            <td>
                <input type='text' name='question'/>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='submit' value="<?php echo _('Create'); ?>"/>
            </td>
        </tr>

    </table>
</form>


