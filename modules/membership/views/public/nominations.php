<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?= _l('membership_nominations'); ?></h4>

<div class="tw-mt-4">
    <a href="<?php echo site_url('membership/client/apply_nomination'); ?>" class="btn btn-primary tw-mb-4">
        <?php echo _l('membership_add_nomination'); ?>
    </a>

    <div class="panel_s">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo _l('membership_available_elections'); ?></h4>
        </div>
        <div class="panel-body">
            <?php if (count($elections) > 0): ?>
                <div class="row">
                    <?php foreach ($elections as $election): ?>
                        <div class="col-md-6 tw-mb-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5><?php echo e($election['title']); ?></h5>
                                </div>
                                <div class="panel-body">
                                    <p><?php echo $election['description'] ? e(substr($election['description'], 0, 100)) . '...' : '-'; ?></p>
                                    <p><strong><?php echo _l('membership_period'); ?>:</strong> 
                                        <?php echo date('M d, Y', strtotime($election['start_date'])); ?> - 
                                        <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
                                    </p>
                                    <p>
                                        <span class="label label-<?php echo $election['status'] == 'active' ? 'success' : ($election['status'] == 'draft' ? 'warning' : 'default'); ?>">
                                            <?php echo ucfirst($election['status']); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted"><?php echo _l('membership_no_elections'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($my_nominations) && count($my_nominations) > 0): ?>
    <div class="panel_s tw-mt-4">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo _l('membership_my_nominations'); ?></h4>
        </div>
        <div class="panel-body">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th><?php echo _l('membership_election'); ?></th>
                        <th><?php echo _l('membership_position'); ?></th>
                        <th><?php echo _l('membership_status'); ?></th>
                        <th><?php echo _l('membership_date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_nominations as $nom): ?>
                        <tr>
                            <td><?php echo e($nom['election_title'] ?? '-'); ?></td>
                            <td><?php echo e($nom['position']); ?></td>
                            <td>
                                <span class="label label-<?php echo $nom['status'] == 'approved' ? 'success' : ($nom['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($nom['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($nom['nominated_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
