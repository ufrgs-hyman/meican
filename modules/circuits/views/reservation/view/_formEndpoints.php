<table class="reservation-point view-point">
    <thead>
        <tr>
            <th colspan="2">
            	<div class="ui-state-default ui-corner-all ui-transparent-button" id="<?= $prefix ?>-show-details" style="float: right; margin-right: 4px; cursor: pointer;">
            		<span id="<?= $prefix ?>-show-details-icon" class="ui-icon-to-button-without-background ui-icon ui-icon-carat-1-n" title="<?= "Toogle"; ?>"></span>
			    </div>
			    <div class="ui-state-default ui-corner-all ui-transparent-button" id="<?= $prefix; ?>-copy-urn" style="float: right; margin: 0 2px 0 4px; cursor: pointer;">
			        <span class="ui-icon-to-button-without-background ui-icon ui-icon-link" title="<?= Yii::t("circuits", "Copy endpoint link"); ?>"></span>
			    </div>
                <span class="title"><?= $label; ?></span>
            </th>
        </tr>
    </thead>
	<tbody>
	    <tr>
	        <td><strong><?= Yii::t("circuits", "Domain"); ?></strong></td>
	        <td>
	        	<label id="<?= $prefix; ?>-dom" title=""><?= Yii::t("circuits", "loading"); ?>...</label>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-net-row">
	        <td><strong><?= Yii::t("circuits", "Network"); ?></strong></td>
	        <td>
	        	<label id="<?= $prefix; ?>-net" title=""><?= Yii::t("circuits", "loading"); ?>...</label>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-dev-row">
	        <td><strong><?= Yii::t("circuits", "Device"); ?></strong></td>
	        <td>
	        	<label id="<?= $prefix; ?>-dev" title=""><?= Yii::t("circuits", "loading"); ?>...</label>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-port-row">
	        <td><strong><?= Yii::t("circuits", "Port"); ?></strong></td>
	        <td>
	        	<label id="<?= $prefix; ?>-port"title=""><?= Yii::t("circuits", "loading"); ?>...</label>
	        	<label id="<?= $prefix; ?>-urn" hidden></label>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-vlan-row">
	        <td><strong>VLAN</strong></td>
	        <td>
	        	<label id="<?= $prefix; ?>-vlan" title=""><?= Yii::t("circuits", "loading"); ?>...</label>
	        </td>
	    </tr>
	</tbody>
</table>