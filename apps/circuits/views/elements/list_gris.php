<table class="list" style="width: 100%">
    
    <thead>
        <tr>
            <?php if ($refresh): ?>
            <th class="listHeader"></th>
            <?php endif; ?>
            <th class="listHeader"><?php echo _("Tool"); ?></th>
            <th class="listHeader"><?php echo _("Global Reservation Identifier (GRI)"); ?></th>
            <th class="listHeader" align="center">
                <?php echo _("Status"); ?>
                <img alt="<?php echo _("loading"); ?>" style="display:none" id="load_dynamic" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif"/>
            </th>
            <th class="listHeader"><?php echo _("Initial Date/Time"); ?></th>
            <th class="listHeader"><?php echo _("Final Date/Time"); ?></th>
        </tr>
    </thead>
                
    <tbody>
        <?php foreach ($gris as $g): ?>
            <tr id="line<?php echo $g->id; ?>">
                <?php if ($refresh): ?>
                <td>
                    <input type="checkbox" id="cancel<?php echo $g->id; ?>" disabled name="cancel_checkbox[]" value="<?php echo $g->id; ?>" onClick="disabelCancelButton(this);"/>
                </td>
                <?php endif; ?>
                <td>
                    OSCARS
                </td>
                <td>
                    <?php echo $g->descr; ?>
                </td>
                <td style="width:80px;">
                    <label id="status<?php echo $g->id; ?>"><?php echo $g->status; ?></label>
                    <img alt="<?php echo _("loading"); ?>" class="load" style="display:none" src="<?php echo $this->url(''); ?>webroot/img/ajax-loader.gif"/>
                </td>
                <td>
                    <?php echo $g->start; ?>
                </td>
                <td>
                    <?php echo $g->finish; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    
</table>