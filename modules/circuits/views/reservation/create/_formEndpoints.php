<table class="reservation-point view-point">
    <thead>
        <tr>
            <th colspan="2">
            	<div class="ui-state-default ui-corner-all ui-transparent-button" id="<?= $prefix ?>-clear-endpoint" style="float: right; margin-right: 2px; cursor: pointer;">
			        <span class="ui-icon-to-button-without-background ui-icon ui-icon-minusthick" title="<?= "Clear endpoint"; ?>"></span>
			    </div>
			    <div class="ui-state-default ui-corner-all ui-transparent-button" id="<?= $prefix ?>-select-current-host" style="float: right; margin-right: 2px; cursor: pointer;" disabled>
			        <span class="ui-icon-to-button-without-background ui-icon ui-icon-home" title="<?= "Select current host"; ?>"></span>
			    </div>
			    <div class="ui-state-default ui-corner-all ui-transparent-button" id="<?= $prefix ?>-search-host" style="float: right; margin-right: 2px; cursor: pointer;" disabled>
			        <span class="ui-icon-to-button-without-background ui-icon ui-icon-search" title="<?= "Search for endpoint"; ?>"></span>
			    </div>
			    <div class="ui-state-default ui-corner-all ui-state-disabled ui-transparent-button" id="<?= $prefix; ?>-copy-urn" style="float: right; margin: 0 2px 0 4px; cursor: pointer;">
			        <span class="ui-icon-to-button-without-background ui-icon ui-icon-link" title="<?= "Copy endpoint link"; ?>"></span>
			    </div>
                <span class="title"><?= $label; ?></span>
            </th>
        </tr>
    </thead>
	<tbody>
	    <tr>
	        <td><strong><?= Yii::t("circuits", "Domain"); ?></strong></td>
	        <td>
	           <select id="<?= $prefix ?>-domain" name="ReservationForm[<?= $prefix ?>_domain]">
	           		<option value="null"><?= Yii::t("circuits", "loading"); ?></option>
	           </select>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-net-row">
	        <td><strong><?= Yii::t("circuits", "Network"); ?></strong></td>
	        <td>
	        	<select id="<?= $prefix ?>-network" disabled="disabled"></select>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-dev-row">
	        <td><strong><?= Yii::t("circuits", "Device"); ?></strong></td>
	        <td>
	            <select id="<?= $prefix ?>-device" disabled="disabled"></select>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-port-row">
	        <td><strong><?= Yii::t("circuits", "Port"); ?></strong></td>
	        <td>
	            <select id="<?= $prefix ?>-port" name="ReservationForm[<?= $prefix ?>_port]" disabled="disabled"></select>
	        </td>
	    </tr>
	    <tr id="<?= $prefix; ?>-vlan-row">
	        <td><strong><?= Yii::t("circuits", "VLAN"); ?></strong></td>
	        <td>
	            <select id="<?= $prefix ?>-vlan" name="ReservationForm[<?= $prefix ?>_vlan]" disabled="disabled"></select>
	        </td>
	    </tr>
	</tbody>
</table>