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
                        <a href="<?php echo admin_url('membership/jobs/add'); ?>" class="btn btn-primary">
                            <?php echo _l('membership_add_job'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mtop15">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (count($jobs) > 0): ?>
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
                                        $this->load->model('clients_model');
                                        $contact = $this->clients_model->get_contact($job['contact_id']);
                                        ?>
                                        <tr>
                                            <td><?php echo $job['title']; ?></td>
                                            <td><?php echo $job['company'] ?: '-'; ?></td>
                                            <td><?php echo $job['location'] ?: '-'; ?></td>
                                            <td>
                                                <?php if ($contact): ?>
                                                    <?php echo $contact->firstname . ' ' . $contact->lastname; ?>
                                                <?php else: ?>
                                                    Unknown
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo ($job['status'] == 'approved' ? 'success' : ($job['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                    <?php echo _l('membership_job_status_' . $job['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($job['id']): ?>
                                                    <a href="<?php echo admin_url('membership/jobs/' . $job['id']); ?>" class="btn btn-default btn-xs">
                                                        <i class="fa fa-pencil-square-o"></i> <?php echo _l('membership_edit'); ?>
                                                    </a>
                                                    <a href="<?php echo admin_url('membership/delete_job/' . $job['id']); ?>" class="btn btn-danger btn-xs"
                                                       onclick="return confirm('<?php echo _l('membership_delete_confirm'); ?>')">
                                                        <i class="fa fa-trash-o"></i> <?php echo _l('membership_delete'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center">
                                <p><?php echo _l('membership_no_jobs'); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (count($pending_jobs) > 0): ?>
                            <div class="mtop15">
                                <h5><?php echo _l('membership_pending_approval'); ?></h5>
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
                                            ?>
                                            <tr>
                                                <td><?php echo $job['title']; ?></td>
                                                <td><?php echo $job['company'] ?: '-'; ?></td>
                                                <td>
                                                    <?php if ($contact): ?>
                                                        <?php echo $contact->firstname . ' ' . $contact->lastname; ?>
                                                    <?php else: ?>
                                                        Unknown
                                                    <?php endif; ?>
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>