<?php

$leftArray = $argsToElement->left;
$rightArray = $argsToElement->right;
$title = $argsToElement->title;

?>

<h1>
    <?php echo $title; ?>
</h1>

<table class="withoutBorder">
   
    <tr>
        <td>
            <select id="unused" name="unusedArray[]" multiple="multiple" size="10" style="width: 45ex">
                <optgroup label="<?php echo _("Available"); ?>">
                    <?php foreach ($leftArray as $la): ?>
                    <option <?php if (!$la->editable) echo 'disabled="disabled"'; ?> value="<?php echo $la->id ?>"><?php echo $la->name ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </td>

        <td>
            <table class="withoutBorder">
                <tr>
                    <td>
                        <div align="center">
                            <input class="add" type="button" onclick="moveOption('unused', 'used');" value="<?php echo _('Add'); ?>" style="float: none"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div align="center">
                            <input class="remove" type="button" onclick="moveOption('used', 'unused');" value="<?php echo _('Remove'); ?>" style="float: none"/>
                        </div>
                    </td>
                </tr>
            </table>
        </td>

        <td>
            <select id="used" name="usedArray[]" multiple="multiple" size="10" style="width: 45ex">
                <optgroup label="<?php echo _("Used"); ?>">
                    <?php foreach ($rightArray as $ra): ?>
                    <option <?php if (!$ra->editable) echo 'disabled="disabled"'; ?> value="<?php echo $ra->id ?>"><?php echo $ra->name ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </td>

    </tr>

</table>
