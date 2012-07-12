<form method='POST' action="<?php echo $this->buildLink(array('action' => 'saveResponse', 'param' => array('loc_id' => $request->loc_id))); ?>">

    <table class="list">
        <tbody>
            <tr>
                <th>
                    <?php echo _('Request domain'); ?>
                </th>
                <td colspan="2">
                    <?php echo $request->src_domain; ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?php echo _('Request user'); ?>
                </th>
                <td colspan="2">
                    <?php echo $request->src_user; ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?php echo _('Destination domain'); ?>
                </th>
                <td colspan="2">
                    <?php echo $request->dst_domain; ?>
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
                <td><?php echo $request->src_domain; ?></td>
                <td><?php echo $request->dst_domain; ?></td>
            </tr>

            <?php if ($request->flow_info): ?>
                <tr>
                    <th><?php echo _('Network'); ?></th>
                    <td><?php echo $request->flow_info->source->network; ?></td>
                    <td><?php echo $request->flow_info->dest->network; ?></td>
                </tr>
                <tr>
                    <th><?php echo _('Device'); ?></th>
                    <td><?php echo $request->flow_info->source->device; ?></td>
                    <td><?php echo $request->flow_info->dest->device; ?></td>
                </tr>
                <tr>
                    <th><?php echo _('Port number'); ?></th>
                    <td><?php echo $request->flow_info->source->port; ?></td>
                    <td><?php echo $request->flow_info->dest->port; ?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <th><?php echo _('Bandwidth'); ?></th>
                <td colspan="2"><?php echo $request->bandwidth; ?></td>
            </tr>

            <?php if ($request->timer_info): ?>
                <tr>
                    <th><?php echo _('Begin'); ?></th>
                    <td colspan="2"><?php echo $request->timer_info['start']; ?></td>
                </tr>
                <tr>
                    <th><?php echo _('End'); ?></th>
                    <td colspan="2"><?php echo $request->timer_info['finish']; ?></td>
                </tr>

                <?php if ($request->timer_info['summary']): ?>
                    <tr>
                        <th><?php echo _('Recurrence'); ?></th>
                        <td colspan="2"><?php echo $request->timer_info['summary']; ?></td>
                    </tr>
                <?php endif; ?>

            <?php endif; ?>

            <?php if ($request->available_bandwidth): ?>
                <tr>
                    <td colspan="3">
                    </td>
                </tr>
                <tr>
                    <th><?php echo _('Available Bandwidth (Mbps)'); ?></th>
                    <?php if (is_int($request->available_bandwidth) && ($request->available_bandwidth <= ($request->capacity * 0.2))): ?>
                        <td colspan="2" style="color: red"><?php echo $request->available_bandwidth; ?></td>
                    <?php else: ?>
                        <td colspan="2"><?php echo $request->available_bandwidth; ?></td>
                    <?php endif; ?>
                </tr>
            <?php endif; ?>

        </tbody>

    </table>

    <br/>
    <br/>

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