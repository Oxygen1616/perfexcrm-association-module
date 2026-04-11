<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_elections'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#electionModal">
                            <?php echo _l('membership_add_election'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($elections) > 0): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_election_title'); ?></th>
                                        <th><?php echo _l('membership_period'); ?></th>
                                        <th><?php echo _l('membership_status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($elections as $election): ?>
                                        <tr>
                                            <td>
                                                <a href="#" onclick="editElection(<?php echo $election['id']; ?>, '<?php echo addslashes($election['title']); ?>', '<?php echo addslashes($election['description']); ?>', '<?php echo date('Y-m-d\TH:i', strtotime($election['start_date'])); ?>', '<?php echo date('Y-m-d\TH:i', strtotime($election['end_date'])); ?>', '<?php echo $election['status']; ?>')">
                                                    <?php echo e($election['title']); ?>
                                                </a>
                                                <div class="row-options">
                                                    <a href="#" onclick="editElection(<?php echo $election['id']; ?>, '<?php echo addslashes($election['title']); ?>', '<?php echo addslashes($election['description']); ?>', '<?php echo date('Y-m-d\TH:i', strtotime($election['start_date'])); ?>', '<?php echo date('Y-m-d\TH:i', strtotime($election['end_date'])); ?>', '<?php echo $election['status']; ?>')">
                                                        <?php echo _l('membership_edit'); ?>
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('membership/election_results/' . $election['id']); ?>">
                                                        <?php echo _l('membership_results'); ?>
                                                    </a>
                                                    |
                                                    <a href="<?php echo admin_url('membership/delete_election/' . $election['id']); ?>"
                                                       onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                        <?php echo _l('membership_delete'); ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($election['start_date'])); ?> -
                                                <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo $election['status'] == 'active' ? 'success' : ($election['status'] == 'draft' ? 'warning' : 'default'); ?>">
                                                    <?php echo ucfirst($election['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_elections'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="electionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo _l('membership_add_election'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/vote_elections')); ?>
            <div class="modal-body">
                <input type="hidden" name="id" id="election_id" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_election_title'); ?> *</label>
                            <input type="text" name="title" id="election_title" class="form-control" required value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_description'); ?></label>
                            <textarea name="description" id="election_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_start_date'); ?></label>
                            <input type="datetime-local" name="start_date" id="election_start_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_end_date'); ?></label>
                            <input type="datetime-local" name="end_date" id="election_end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo _l('membership_status'); ?></label>
                            <select name="status" id="election_status" class="form-control">
                                <option value="draft"><?php echo _l('membership_draft'); ?></option>
                                <option value="active"><?php echo _l('membership_active'); ?></option>
                                <option value="closed"><?php echo _l('membership_closed'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('membership_cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('membership_save'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function editElection(id, title, description, start_date, end_date, status) {
    document.getElementById('election_id').value = id;
    document.getElementById('election_title').value = title;
    document.getElementById('election_description').value = description;
    document.getElementById('election_start_date').value = start_date;
    document.getElementById('election_end_date').value = end_date;
    document.getElementById('election_status').value = status;
    document.querySelector('#electionModal .modal-title').textContent = '<?php echo _l('membership_edit_election'); ?>';
    $('#electionModal').modal('show');
}

$('#electionModal').on('hidden.bs.modal', function() {
    document.getElementById('election_id').value = '';
    document.getElementById('election_title').value = '';
    document.getElementById('election_description').value = '';
    document.getElementById('election_start_date').value = '';
    document.getElementById('election_end_date').value = '';
    document.getElementById('election_status').value = 'draft';
    document.querySelector('#electionModal .modal-title').textContent = '<?php echo _l('membership_add_election'); ?>';
});
</script>

<?php init_tail(); ?>
