<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-inline">
                    <li class="col-md-6">
                        <h4><?php echo _l('membership_jobs'); ?></h4>
                    </li>
                    <li class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary" onclick="openAddJobModal()">
                            <i class="fa fa-plus"></i> <?php echo _l('membership_post_job'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Pending Member Jobs -->
        <?php if (!empty($pending_jobs)): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold" style="color:#d9822b;">
                            <i class="fa fa-clock-o"></i>
                            <?php echo _l('membership_pending_approval'); ?>
                            <span class="badge" style="background:#d9822b;"><?= count($pending_jobs); ?></span>
                        </h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo _l('membership_job_title'); ?></th>
                                    <th><?php echo _l('membership_job_company'); ?></th>
                                    <th><?php echo _l('membership_job_posted_by'); ?></th>
                                    <th><?php echo _l('membership_actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_jobs as $job): ?>
                                    <?php
                                    $this->load->model('clients_model');
                                    $contact = $this->clients_model->get_contact($job['contact_id']);
                                    $poster = $contact ? $contact->firstname . ' ' . $contact->lastname : _l('membership_unknown');
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($job['title']); ?></strong>
                                            <?php if ($job['location']): ?>
                                                <br><small class="text-muted"><i class="fa fa-map-marker"></i> <?= e($job['location']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($job['company']) ?: '-'; ?></td>
                                        <td>
                                            <span class="label label-info" style="font-size:10px;">Member</span>
                                            <?= e($poster); ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo admin_url('membership/approve_job/' . $job['id']); ?>" class="btn btn-success btn-xs">
                                                <i class="fa fa-check"></i> <?php echo _l('membership_approve'); ?>
                                            </a>
                                            <a href="<?php echo admin_url('membership/reject_job/' . $job['id']); ?>" class="btn btn-danger btn-xs">
                                                <i class="fa fa-times"></i> <?php echo _l('membership_reject'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- All Jobs -->
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (!empty($jobs)): ?>
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('membership_job_title'); ?></th>
                                        <th><?php echo _l('membership_job_company'); ?></th>
                                        <th><?php echo _l('membership_job_location'); ?></th>
                                        <th><?php echo _l('membership_job_posted_by'); ?></th>
                                        <th><?php echo _l('membership_job_status'); ?></th>
                                        <th><?php echo _l('membership_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jobs as $job): ?>
                                        <?php
                                        $posted_by_type = isset($job['posted_by_type']) ? $job['posted_by_type'] : 'member';
                                        if ($posted_by_type === 'admin') {
                                            $pname = !empty($job['posted_by_name']) ? $job['posted_by_name'] : _l('membership_admin');
                                            $poster_label = '<span class="label label-default" style="font-size:10px;">Admin</span> ' . e($pname);
                                        } else {
                                            $this->load->model('clients_model');
                                            $contact = $this->clients_model->get_contact($job['contact_id']);
                                            $poster_label = $contact
                                                ? '<span class="label label-info" style="font-size:10px;">Member</span> ' . e($contact->firstname . ' ' . $contact->lastname)
                                                : _l('membership_unknown');
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($job['title']); ?></strong>
                                                <?php if (!empty($job['external_url'])): ?>
                                                    <br><small><a href="<?= e($job['external_url']); ?>" target="_blank"><i class="fa fa-external-link"></i> <?= _l('membership_apply_link'); ?></a></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($job['company']) ?: '-'; ?></td>
                                            <td><?php echo e($job['location']) ?: '-'; ?></td>
                                            <td><?= $poster_label; ?></td>
                                            <td>
                                                <span class="label label-<?php echo ($job['status'] == 'approved' ? 'success' : ($job['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                    <?php echo _l('membership_job_status_' . $job['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-default btn-xs" onclick="editJob(<?= $job['id']; ?>, <?= htmlspecialchars(json_encode($job)); ?>)">
                                                    <i class="fa fa-pencil-square-o"></i> <?php echo _l('membership_edit'); ?>
                                                </button>
                                                <a href="<?php echo admin_url('membership/delete_job/' . $job['id']); ?>"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                    <i class="fa fa-trash-o"></i> <?php echo _l('membership_delete'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center text-muted"><?php echo _l('membership_no_jobs'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Job Modal -->
<div class="modal fade" id="jobModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="jobModalTitle"><?php echo _l('membership_post_job'); ?></h4>
            </div>
            <?php echo form_open(admin_url('membership/jobs'), ['id' => 'jobForm']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_job_title'); ?> *</label>
                            <input type="text" name="title" id="job_title" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_job_company'); ?></label>
                            <input type="text" name="company" id="job_company" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_job_location'); ?></label>
                            <input type="text" name="location" id="job_location" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo _l('membership_job_salary'); ?></label>
                            <input type="text" name="salary_range" id="job_salary" class="form-control" placeholder="e.g. $50,000 – $70,000">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_job_apply_link'); ?></label>
                    <input type="url" name="external_url" id="job_external_url" class="form-control" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label><?php echo _l('membership_job_description'); ?> *</label>
                    <textarea name="description" id="job_description" class="form-control" rows="6" required></textarea>
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
function openAddJobModal() {
    $('#jobModalTitle').text('<?= _l('membership_post_job'); ?>');
    $('#jobForm').attr('action', '<?= admin_url('membership/jobs'); ?>');
    $('#job_title, #job_company, #job_location, #job_salary, #job_external_url, #job_description').val('');
    $('#jobModal').modal('show');
}

function editJob(id, job) {
    $('#jobModalTitle').text('<?= _l('membership_edit_job'); ?>');
    $('#jobForm').attr('action', '<?= admin_url('membership/jobs/'); ?>' + id);
    $('#job_title').val(job.title || '');
    $('#job_company').val(job.company || '');
    $('#job_location').val(job.location || '');
    $('#job_salary').val(job.salary_range || '');
    $('#job_external_url').val(job.external_url || '');
    $('#job_description').val(job.description || '');
    $('#jobModal').modal('show');
}

$('#jobModal').on('hidden.bs.modal', function() {
    $('#job_title, #job_company, #job_location, #job_salary, #job_external_url, #job_description').val('');
});
</script>
<?php init_tail(); ?>
