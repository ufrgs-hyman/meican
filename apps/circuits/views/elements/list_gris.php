<table class="list" style="width: 100%">
    
    <thead>
        <tr>
            <th class="listHeader" style="width: 80px;"></th>
            <th class="listHeader"><?php echo _("Tool"); ?></th>
            <th class="listHeader"><?php echo _("GRI"); ?></th>
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
            <tr id="line<?php echo $g->id; ?>" class="<?php echo @'reservation-status-'.strtolower($g->original_status); ?>">
                <td>
                <?php if (!empty($refresh)): ?>
                    <input type="checkbox" id="cancel<?php echo $g->id; ?>" disabled name="cancel_checkbox[]" value="<?php echo $g->id; ?>" onClick="disabelCancelButton(this);"/>
                <?php endif; ?>
                <?php if (!empty($authorization)): ?>
                    <?php if (true || in_array($g->original_status, array("ACTIVE", "PENDING"))): ?>
                        <img alt="<?php echo _("Accept"); ?>" class="link-button" onclick="request.reply('accept');" src="<?php echo $this->url(''); ?>webroot/img/hand_good.png"/>
                        <img alt="<?php echo _("Reject"); ?>" class="link-button" onclick="request.reply('reject');" src="<?php echo $this->url(''); ?>webroot/img/hand_bad.png"/>
                    <?php else: ?>
                        <img alt="<?php echo _("Edit"); ?>" class="link-button" src="<?php echo $this->url(''); ?>webroot/img/edit.png"/>
                    <?php endif; ?>
                <?php endif; ?>
                </td>
                <td>
                    OSCARS
                </td>
                <td>
                    <?php echo $g->descr; ?>
                </td>
                <td> <?php /* style="width:80px;"*/ ?>
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
