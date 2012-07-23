<?php $processes = $this->passedArgs; ?>

<h1>Processos</h1>

<table class="list">
    <tr>
        <th></th>
        <th><?php echo _('Process'); ?></th>
        <th><?php echo _('Version'); ?></th>
        <th><?php echo _('PID'); ?></th>
        <th><?php echo _('Status'); ?></th>
        <th><?php echo _('In progress'); ?></th>
        <th><?php echo _('Completed'); ?></th>
        <th><?php echo _('Error'); ?></th>
        <th><?php echo _('Failed'); ?></th>
        <th><?php echo _('Suspended'); ?></th>
    </tr>

    <?php foreach ($processes as $key => $process) : ?>

    <tr>
        <td>
            <input class="cancel" type="button" value="<?php echo _('Undeploy'); ?>" onclick="redir('<?php echo $this->buildLink(array('action' => 'undeployProcess', 'param' => array('package' => $process->{"deployment-info"}->package))); ?>')" />
        </td>
        <td><?php echo $process->{"deployment-info"}->package; ?></td>
        <td><?php echo $process->version; ?></td>
        <td><?php echo $process->pid; ?></td>
        <td><?php echo $process->status; ?></td>
        <td><?php echo $process->{"instance-summary"}->instances[0]->count; ?></td>
        <td><?php echo $process->{"instance-summary"}->instances[1]->count; ?></td>
        <td><?php echo $process->{"instance-summary"}->instances[2]->count; ?></td>
        <td><?php echo $process->{"instance-summary"}->instances[3]->count; ?></td>
        <td><?php echo $process->{"instance-summary"}->instances[4]->count; ?></td>
       
    </tr>
    <?php endforeach; ?>
</table>

<h1><?php echo _('Deploy process'); ?></h1>

<form action="" method="post" id="upload_form" enctype="multipart/form-data">
    <table class="add">
        <tr>
            <th class="right">
                <label for='name_process'><?php echo _('Name'); ?>:</label>
            </th>
            <td class="left">
                <input type='text' name='name_process'/>
            </td>
        </tr>
        <tr>
            <th class="right">
                <label for='file_to_deploy'><?php echo _('File'); ?>:</label>
            </th>
            <td class="left">
		<input type="file" name="upload" id="upload" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input class="ok" type="button" value="<?php echo _('Ok'); ?>" onclick="deploy();" />
            </td>
        </tr>
    </table>
</form>

            


