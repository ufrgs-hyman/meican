<?php 

use yii\helpers\Url;

$this->params['header'] = [$model->name, ['Home', 'Users', $model->name]];

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("aaa", "Profile"); ?></h3>
            </div>
            <div class="box-body">                
                <?= $this->render("_profile", ['model'=>$model]); ?>
            </div>
            <div class="box-footer">
                <a href="<?= Url::to(["update", 'id'=>$model->id]) ?>" class="btn btn-default"><i class="fa fa-pencil"></i> Edit</a>
            </div>   
        </div>
    </div>
    <div class="col-md-6">
        <?= $this->render("@meican/aaa/views/role/_index", ['rolesProvider'=>$rolesProvider]); ?>
    </div>
</div>

        
