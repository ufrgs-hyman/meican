<?php extract(get_object_vars($request)); ?>
<h1><?php echo _("Reply request"); ?></h1>
<h4 style="margin:0;" class="float-left">
    <dl>
        <dt><?php echo _("Reservation name"); ?></dt>
        <dd><?php echo $res_name; ?></dd>
        <dt><?php echo _("Requester Domain"); ?></dt>
        <dd><?php echo $flow->source->domain; ?></dd> 
        <dt><?php echo _("Requester User"); ?></dt>
        <dd><?php echo $usr_login; ?></dd>
    </dl>
</h4>

<div class="float-right" style="padding-left: 4px;">
    <?php if ($gris): ?>
        <form method="POST" style="min-height:64px;width:100%;min-width: 550px;" action="<?php echo $this->buildLink(array('action' => 'cancel', 'param' => "res_id:$res_id,refresh:1")); ?>">    
            <?php if (!empty($refresh)): ?>
                <div class="controls">
                    <input type="button" class="refresh" value="<?php echo _("Refresh") ?>" onclick="griRefreshStatus(<?php echo $res_id; ?>);" />
                    <input type="submit" class="cancel" disabled="disabled" id="cancel_button" value="<?php echo _("Cancel reservations"); ?>" onclick="return confirm('<?php echo _('Cancel the selected reservations?'); ?>')"/>
                </div>
            <?php endif; ?>
            <?=
            $this->element('list_gris',
                    compact('gris', 'refresh') + array('app' => 'circuits', 'authorization' => true));
            ?>
        </form>
    <?php endif; ?>
</div>


<div style="clear:both;"></div>

<div id="subtab-map" class="tab_subcontent shadow-box">
    <div id="res_mapCanvas" style="width:400px; height:400px;"></div>    
</div>
<div id="calendar" class="float-right" style="width:550px;height: 402px;"></div>
<div id="subtab-points" class="tab_subcontent float-right" style="padding-left:8px;">
    <?=
    $this->element('view_point',
            array('app' => 'circuits', 'type' => 'source', 'flow' => $flow));
    ?>
    <div id="bandwidth_bar">
        <div id="bandwidth_bar_text">
            <div style="text-align:center;">
                <label id="lb_bandwidth"><?php echo $bandwidth . " " . _("Mbps") ?></label>
            </div>
        </div>
        <div id="bandwidth_bar_inside" style="width: <?= round($bandwidth * 100 / 1000); //TODO: calcular       ?>%"></div>
    </div>
    <?=
    $this->element('view_point',
            array('app' => 'circuits', 'type' => 'destination', 'flow' => $flow));
    ?>
</div>

<div style="clear:both;"></div>

<div id="tabs-2" class="tab_content">
    <?=
    $this->element('view_timer', array('app' => 'circuits', 'timer' => $timer));
    ?>
    <?=
    false && $request ? $this->element('view_request',
                            compact('request') + array('app' => 'circuits')) : null;
    ?>
</div>
<div id="tabs-4" class="control_tab">
    <input type="button" id="bc1" class="cancel" value="<?php echo _('Back'); ?>" onclick="redir('<?= $this->url(array("action" => "show")); ?>');"/>
</div>
<div id="dialog-form" title="<?= _("Authorization"); ?>">
    <form>
        <div id="MessageBandwidth"></div>
        <img id="MessageImg" alt="" src=""/>
        <label for="name" id="MessageLabel">Provide a message</label>
        <textarea type="text" name="name" id="Message" class="text ui-widget-content ui-corner-all" style="width:100%;margin-top:10px;" cols="20" rows="5"></textarea>
    </form>
</div>
<?php
/* <div id="tabs-4" class="control_tab">
  <form method="POST" id="FormReply" action="<?php echo $this->buildLink(array('action' => 'saveResponse', 'param' => array('loc_id' => $request->loc_id))); ?>">
  <div>
  <label for="response"><?php echo _('Response'); ?></label>
  <input type="radio" name="response" value="accept"/><?php echo _('ACCEPT'); ?>
  <input type="radio" name="response" value="reject"/><?php echo _('REJECT'); ?>
  </div>
  <label for="message"><?php echo _('Message'); ?></label>
  <input type="text" id="Message" name="message" size="120"/>

  <input class="ok" type='submit' value='<?php echo _('Reply'); ?>'/>
  </form>
  </div> */

$events = array();
$i = 5;
foreach ($gris as $gri) {
    $i++;
    $events[] = array(
        'id' => $i,
        'start' => strtotime($gri->start_date) * 1000,
        'end' => strtotime($gri->finish_date) * 1000,
        'title' => '',
        'hclass' => 'reservation-status-' . strtolower($gri->original_status)
    );
}
foreach ($calendar_gris as $gri) {
    $i++;
    $events[] = array(
        'id' => $i,
        'start' => strtotime($gri->start_date) * 1000,
        'end' => strtotime($gri->finish_date) * 1000,
        'title' => '',
        'hclass' => 'cal-old'
    );
}

/* .concat([                    
  {
  "id":1,
  "start": new Date(year, month, day, 12),
  "end": new Date(year, month, day, 13, 30),
  "title":"Reservation1",
  "status": 1
  },
  {
  "id":2,
  "start": new Date(year, month, day, 14),
  "end": new Date(year, month, day, 14, 45),
  "title":"Reservation2",
  "class": "test2",
  "status": -1
  },
  {
  "id":3,
  "start": new Date(year, month, day + 1, 12),
  "end": new Date(year, month, day + 1, 17, 45),
  "title":"Reservation3",
  "status": 0
  },
  {
  "id":4,
  "start": new Date(year, month, day - 1, 8),
  "end": new Date(year, month, day - 1, 9, 30),
  "title":"Reservation4",
  "status": -1
  },
  {
  "id":5,
  "start": new Date(year, month, day + 1, 14),
  "end": new Date(year, month, day + 1, 15),
  "title":"Reservation5",
  "status": 1
  },
  {
  "id":6,
  "start": new Date(year, month, day + 1, 14),
  "end": new Date(year, month, day + 1, 15),
  "title":"Reservation7",
  "status": 1
  },
  {
  "id":6,
  "start": new Date(year, month, day, 10),
  "end": new Date(year, month, day, 11),
  "title":"Reservation6 (read only)",
  "status": 0,
  readOnly : true
  }

  ]) */
?>

<script type="text/javascript" src="<?php echo $this->url(); ?>webroot/js/jquery.weekcalendar.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->url(); ?>webroot/css/jquery.weekcalendar.css" />

<link rel='stylesheet' type='text/css' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css' />
<script type="text/javascript" src="<?php echo $this->url(); ?>apps/circuits/webroot/js/reservations.js"></script>
<script type="text/javascript" src="<?php echo $this->url(); ?>apps/circuits/webroot/js/reservations_view.js"></script>

<script type="text/javascript">
    request.setBandwidth('<?= $bandwidth ?>');
    request.setActionUrl('<?= $this->url(array('action' => 'saveResponse', 'param' => array('loc_id' => $request->loc_id))) ?>');
    request.set({gris: <?= json_encode($events); ?>});
    request.buildCalendar();
</script>

<a href="<?= $this->url(array('action' => 'saveResponse', 'param' => array('loc_id' => $request->loc_id))) ?>" id="UrlPost" style="display:none;"></a>
<div id="Response" sytle="display:none;"></div>