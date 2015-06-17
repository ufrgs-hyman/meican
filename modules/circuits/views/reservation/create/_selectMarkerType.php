<table class="reservation-marker-type reservation-point view-point">
    <thead>
        <tr>
            <th colspan="2">
                <span class="title"><?= Yii::t("circuits", "Marker type"); ?></span>
            </th>
        </tr>
    </thead>
	<tbody>
	    <tr>
	        <td>
		    	<input id="marker-type-network" type="radio" name="marker-type" value="network" checked></input>
		    	<label for="marker-type-network"> <?= Yii::t("circuits", "Network"); ?></label>
		    	<input id="marker-type-device" type="radio" name="marker-type" value="device"></input>
		    	<label for="marker-type-device"> <?= Yii::t("circuits", "Device"); ?></label>
		    </td>
	    </tr>
	</tbody>
</table>
</span>