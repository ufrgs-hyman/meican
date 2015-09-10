<div id="map-type-box" style="margin-left: 10px; margin-top:15px;" hidden>
    <select id="map-type-select" style="width: 80px;">
      <option value="r"><?= Yii::t("circuits", "Map"); ?></option>
      <option value="cr"><?= Yii::t("circuits", "Clean"); ?></option>
      <option value="t"><?= Yii::t("circuits", "Terrain"); ?></option>
      <option value="s"><?= Yii::t("circuits", "Satellite"); ?></option>
      <option value="h"><?= Yii::t("circuits", "Hybrid"); ?></option>
    </select>
</div>

<div id="marker-type-box" style="margin-left: 10px; margin-top:15px;" hidden>
    <select id="marker-type-select" style="width: 95px;">
      <option value="dev"><?= Yii::t("circuits", "Devices"); ?></option>
      <option value="net"><?= Yii::t("circuits", "Networks"); ?></option>
    </select>
</div>