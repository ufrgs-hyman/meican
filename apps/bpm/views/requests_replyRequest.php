<?php
$args = $this->passedArgs;
$request = $args->request;
?>

<form method='POST' action="<?php echo $this->buildLink( array('action' => 'saveResponse', 'param' => array('loc_id' => $request->loc_id))); ?>">

    <table class="list">
        <tbody>
        <tr>
            <th>
                <?php echo _('Request domain'); ?>
            </th>
            <td colspan="2">
                <?php echo $request->dom_src; ?>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo _('Request user'); ?>
            </th>
            <td colspan="2">
                <?php echo $request->usr_src; ?>
            </td>
        </tr>

        <tr>
            <td colspan="3">

            </td>

        </tr>
        <tr>
            <th colspan="3">
                <?php echo _('Reservation details'); ?>
            </th>
        </tr>
                    <tr>
                        <td></td>
                        <th><?php echo _('Source'); ?></th>
                        <th><?php echo _('Destination'); ?></th>
                    </tr>
                    <tr>
                        <th><?php echo _('Domain'); ?></th>
                        <td><?php echo $request->flow_info['src_dom']; ?></td>
                        <td><?php echo $request->flow_info['dst_dom']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo _('Network'); ?></th>
                        <td><?php echo $request->src_endpoint['net_descr']; ?></td>
                        <td><?php echo $request->dst_endpoint['net_descr']; ?></td>
                    </tr>
                     <tr>
                        <th><?php echo _('Device'); ?></th>
                        <td><?php echo $request->src_endpoint['dev_descr']; ?></td>
                        <td><?php echo $request->dst_endpoint['dev_descr']; ?></td>
                    </tr>
                     <tr>
                        <th><?php echo _('Port number'); ?></th>
                        <td><?php echo $request->src_endpoint['port_number']; ?></td>
                        <td><?php echo $request->dst_endpoint['port_number'];  ?></td>
                    </tr>
                    <tr>
                        <th><?php echo _('Bandwidth'); ?></th>
                        <td colspan="2"><?php echo $request->flow_info['bandwidth']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo _('Begin'); ?></th>
                        <td colspan="2"><?php echo $request->timer_info['start']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo _('End'); ?></th>
                        <td colspan="2"><?php echo $request->timer_info['finish']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo _('Recurrence'); ?></th>
                        <td colspan="2"><?php echo $request->timer_info['recurrence']; ?></td>
                    </tr>
     
        </tbody>
       
    </table>

    <br>
    <br>
    
    <table>
        <tr>
            <th>
                <?php echo _('Response'); ?>
            </th>
            <td>
                <input type='radio' name='response' value="accept"/><?php echo _('ACCEPT'); ?>
                <input type='radio' name='response' value="reject"/><?php echo _('REJECT'); ?>
            </td>
        </tr>

        <tr>
            <th>
                <?php echo _('Message'); ?>
            </th>
            <td>
                <input type='text' name='message' size="120"/>
            </td>
        </tr>

        <tr>
            <td colspan='2'>
                <input class="ok" type='submit' value='Responder'/>
            </td>
        </tr>

    </table>
</form>



