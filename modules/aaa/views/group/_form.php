<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

use meican\aaa\models\Group;

$this->params['header'] = [Yii::t('topology', 'Domains'), ['Home', 'Topology', 'Domains']];

$form=ActiveForm::begin(array(
    'method'    => 'post',
    'layout'    => 'horizontal'
));

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $this->params['box-title']; ?></h3>
    </div>
    <div class="box-body">
        <div class="formAccessControl input">
            <?= $form->field($group,'name'); ?>
        </div>
        
        <div id="type-div">
            <?php echo $form->field($group,'type')->dropDownList([Group::TYPE_DOMAIN => Yii::t('aaa', 'Domain'), Group::TYPE_SYSTEM => Yii::t('aaa', 'System')]); ?>
        </div>
    
        <table class="table table-striped table-bordered icheck" id=<?= Group::TYPE_DOMAIN?> <?php if($group->type!=Group::TYPE_DOMAIN && isset($group->type)) echo 'style="display: none"';?>>
            <thead>
                <tr>
                    <th style="width: 40%"><?= Yii::t("aaa", 'Modules'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Create'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Read'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Update'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Delete'); ?></th>
                </tr>
            </thead>
    
            <tbody>
                <?php     
                    //Set table apps (check value in case of edition)
                    $table = '';
                    
                    foreach($apps as $appValue=>$appName) {
                        $table .= "<tr>";
                        $table .= "<td>$appName</td>";
                        
                        //Capitalize first letter
                        $appValue = ucfirst($appValue);
                        
                        
                        if(isset($childsChecked) && in_array("create$appValue", $childsChecked)) {
                            $table .= "<td><input name='Permissions[]' value='create$appValue' type='checkbox' checked></td>";
                        }
                        else {
                            $table .= "<td><input name='Permissions[]' value='create$appValue' type='checkbox'></td>";
                        }
                        
                        
                        if($appValue != 'Waypoint'){
                            if(isset($childsChecked) && in_array("read$appValue", $childsChecked)) {
                                $table .= "<td><input name='Permissions[]' value='read$appValue' type='checkbox' checked></td>";
                            }
                            else {
                                $table .= "<td><input name='Permissions[]' value='read$appValue' type='checkbox'></td>";
                            }
                        } else {
                            $table .= "<td><input name='Permissions[]' value='read$appValue' type='checkbox' disabled></td>";
                        }
                        
                        if($appValue != 'Waypoint'){
                            if(isset($childsChecked) && in_array("update$appValue", $childsChecked)) {
                                $table .= "<td><input name='Permissions[]' value='update$appValue' type='checkbox' checked></td>";
                            }
                            else {
                                $table .= "<td><input name='Permissions[]' value='update$appValue' type='checkbox'></td>";
                            }
                        } else {
                            $table .= "<td><input name='Permissions[]' value='update$appValue' type='checkbox' disabled></td>";
                        }
                        
                        if($appValue != 'Waypoint'){
                            if(isset($childsChecked) && in_array("delete$appValue", $childsChecked)) {
                                $table .= "<td><input name='Permissions[]' value='delete$appValue' type='checkbox' checked></td>";
                            }
                            else {
                                $table .= "<td><input name='Permissions[]' value='delete$appValue' type='checkbox'></td>";
                            }
                        } else {
                            $table .= "<td><input name='Permissions[]' value='delete$appValue' type='checkbox' disabled></td>";
                        }
                        
                        $table .= "</tr>";    
                    }
                    
                    echo $table;
                ?>
            </tbody>
    
        </table>
        
        <table class="table table-striped table-bordered icheck" id=<?= Group::TYPE_SYSTEM?> <?php if($group->type!=Group::TYPE_SYSTEM) echo 'style="display: none"';?>>
            <thead>
                <tr>
                    <th style="width: 40%"><?= Yii::t("aaa", 'Modules'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Create'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Read'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Update'); ?></th>
                    <th style="width: 15%"><?= Yii::t("aaa", 'Delete'); ?></th>
                </tr>
            </thead>
    
            <tbody>
                <?php     
                    //Set table apps (check value in case of edition)
                    $table = '';
                    
                    foreach($root as $appValue=>$appName) {
                        $table .= "<tr>";
                        $table .= "<td>$appName</td>";
                        
                        //Capitalize first letter
                        $appValue = ucfirst($appValue);

                        if($appValue != 'Configuration'){
                            if(isset($childsChecked) && in_array("create$appValue", $childsChecked)) {
                                $table .= "<td><input name='Permissions1[]' value='create$appValue' type='checkbox' checked></td>";
                            }
                            else {
                                $table .= "<td><input name='Permissions1[]' value='create$appValue' type='checkbox'></td>";
                            }
                        }
                        else $table .= "<td><input name='Permissions1[]' value='create$appValue' type='checkbox' disabled></td>";
                        
                        if(isset($childsChecked) && in_array("read$appValue", $childsChecked)) {
                            $table .= "<td><input name='Permissions1[]' value='read$appValue' type='checkbox' checked></td>";
                        }
                        else {
                            $table .= "<td><input name='Permissions1[]' value='read$appValue' type='checkbox'></td>";
                        }
                        
                        if(isset($childsChecked) && in_array("update$appValue", $childsChecked)) {
                            $table .= "<td><input name='Permissions1[]' value='update$appValue' type='checkbox' checked></td>";
                        }
                        else {
                            $table .= "<td><input name='Permissions1[]' value='update$appValue' type='checkbox'></td>";
                        }
                        
                        if($appValue != 'Configuration'){
                            if(isset($childsChecked) && in_array("delete$appValue", $childsChecked)) {
                                $table .= "<td><input name='Permissions1[]' value='delete$appValue' type='checkbox' checked></td>";
                            }
                            else {
                                $table .= "<td><input name='Permissions1[]' value='delete$appValue' type='checkbox'></td>";
                            }
                        }
                        else $table .= "<td><input name='Permissions1[]' value='delete$appValue' type='checkbox' disabled></td>";
                        
                        $table .= "</tr>";    
                    }
                    
                    echo $table;
                ?>
            </tbody>
    
        </table>
    </div>

    <div class="box-footer">
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-primary"><?= Yii::t("aaa", 'Save'); ?></button>
            </div>
        </div>
    </div>

</div>

<?php ActiveForm::end(); ?>