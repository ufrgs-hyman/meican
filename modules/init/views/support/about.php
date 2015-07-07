<?php 

use yii\helpers\Html;

?>

<div id="container_info">

    <div class="left_info">
        <div class="info_div_img">
            <?= Html::img("@web/images/meican_new.png", ['class'=>'info_img_logo', 'alt' => 'MEICAN']); ?>
            <h2 class="info_title">Management Environment of Inter-domain Circuits for Advanced Networks</h2>
        </div> 
    </div>
    <div class="right_info">
        <div class="info_div_text">
            <div class="inner_text" style="padding-right: 20%;"><br><br>
                <p><b>Management Environment of Inter-domain Circuits for Advanced Networks</b> <?= Yii::t("init", 'is a Web application that enables users to request VCs between well-defined end-points that, depending on operation policies and human authorisation located in the intermediate domains that connect source and destination end-points.'); ?></p>
                <p><?= Yii::t("init", 'Our solution uses Business Process Management (BPM) concepts for managing the VCs establishment process, since VC requested by end-user to network devices configurations.'); ?></p>
                <p><?= Yii::t("init", 'The main contribution of the proposed solution is to provide dynamic authorization strategies composed for policies and human support.'); ?></p>
            </div>
        </div>
    </div> 
</div>

<div style="margin-left: 4%;">

<h1><?= Yii::t("init", 'Version');?></h1>
<p><?= Yii::$app->params['meican.version']; ?></p>

<h1><?= Yii::t("init", 'Documentation'); ?></h1>
<p><?= Yii::t("init", 'The documentation is only in portuguese and is available on <a href="{url}" target="blank">RNP Wiki</a>.</p>', ['url'=> 'https://wiki.rnp.br/display/secipo/Guia+MEICAN']); ?>

<h1><?= Yii::t("init", 'License'); ?></h1>
<p><?= Yii::t("init", 'MEICAN is licenced under BSD2 License.');?></p>

<h1><?= Yii::t("init", 'Developers'); ?></h1>
<p>
<table>
<tr>
	<td>Maurício Quatrin Guerreiro</td><td><?= Html::img("@web/images/support/mqg.gif"); ?></td>
</tr>
<tr>
	<td>Diego Pittol</td><td><?= Html::img("@web/images/support/diego.gif"); ?></td>
</tr>
</table>
</p>

<h1><?= Yii::t("init", 'Previous developers');?></h1>
<p>
<table>
<tr>
	<td>Henrique Resende</td>
</tr>
<tr>
	<td>Pedro Isolani</td>
</tr>
<tr>
	<td>Luine Gallois</td>
</tr>
<tr>
	<td>Augusto Ferreira</td>
</tr>
<tr>
	<td>Leonardo Faganello</td>
</tr>
<tr>
	<td>Luís Armando Bianchin</td>
</tr>
<tr>
	<td>Jair Santanna</td>
</tr>
<tr>
	<td>Felipe Nesello</td>
</tr>
</table>
</p>

<h1><?= Yii::t("init", 'Source code');?></h1>
<p><?= Yii::t("init", 'The project is hosted by');?> <a href="https://github.com/ufrgs-hyman/meican2" target="blank">GitHub</a>.</p>

</div>