<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>

        <div class="row">
            <div class="col-md-8">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <i class="fa fa-briefcase tw-mr-2 tw-text-primary-500"></i>
                    <?php echo _l('membership_jobs'); ?>
                </h4>
            </div>
            <div class="col-md-4 text-right">
                <a href="<?php echo site_url('membership/client/post_job'); ?>" class="btn btn-primary">
                    <i class="fa fa-plus"></i> <?php echo _l('membership_post_job'); ?>
                </a>
            </div>
        </div>

        <div class="row tw-mt-4">
            <div class="col-md-12">
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="panel_s tw-mb-4">
                            <div class="panel-body">
                                <div class="tw-flex tw-items-start tw-justify-between tw-flex-wrap tw-gap-3">
                                    <div class="tw-flex-1 tw-min-w-0">
                                        <h5 class="tw-font-semibold tw-text-neutral-800 tw-mb-1"><?= e($job['title']); ?></h5>
                                        <div class="tw-flex tw-flex-wrap tw-gap-3 tw-text-sm tw-text-neutral-500 tw-mb-2">
                                            <?php if ($job['company']): ?>
                                                <span><i class="fa fa-building-o tw-mr-1"></i><?= e($job['company']); ?></span>
                                            <?php endif; ?>
                                            <?php if ($job['location']): ?>
                                                <span><i class="fa fa-map-marker tw-mr-1"></i><?= e($job['location']); ?></span>
                                            <?php endif; ?>
                                            <?php if ($job['salary_range']): ?>
                                                <span><i class="fa fa-money tw-mr-1"></i><?= e($job['salary_range']); ?></span>
                                            <?php endif; ?>
                                            <span class="tw-text-neutral-400">
                                                <?php
                                                // Show posted by
                                                $ptype = isset($job['posted_by_type']) ? $job['posted_by_type'] : 'member';
                                                if ($ptype === 'admin') {
                                                    echo '<span class="label label-default" style="font-size:10px;">' . _l('membership_admin') . '</span>';
                                                } else {
                                                    echo '<span class="label label-info" style="font-size:10px;">' . _l('membership_member') . '</span>';
                                                }
                                                ?>
                                                &nbsp;<?= _d($job['created_at']); ?>
                                            </span>
                                        </div>
                                        <p class="tw-text-sm tw-text-neutral-600 tw-mb-0">
                                            <?= nl2br(e(substr($job['description'], 0, 300))); ?><?= strlen($job['description']) > 300 ? '…' : ''; ?>
                                        </p>
                                    </div>
                                    <?php if (!empty($job['external_url'])): ?>
                                        <div class="tw-shrink-0">
                                            <a href="<?= e($job['external_url']); ?>" target="_blank" class="btn btn-primary btn-sm">
                                                <i class="fa fa-external-link tw-mr-1"></i><?= _l('membership_apply_now'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="panel_s">
                        <div class="panel-body text-center">
                            <i class="fa fa-briefcase fa-3x text-muted tw-mb-3"></i>
                            <p class="text-muted"><?php echo _l('membership_no_jobs'); ?></p>
                            <a href="<?php echo site_url('membership/client/post_job'); ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i> <?php echo _l('membership_be_first_to_post'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- My Submitted Jobs -->
        <?php if (!empty($my_jobs)): ?>
        <div class="row tw-mt-2">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5 class="tw-font-semibold tw-text-neutral-700 tw-mb-3">
                            <i class="fa fa-list tw-mr-1"></i><?= _l('membership_my_jobs'); ?>
                        </h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?= _l('membership_job_title'); ?></th>
                                    <th><?= _l('membership_job_company'); ?></th>
                                    <th><?= _l('membership_job_status'); ?></th>
                                    <th><?= _l('membership_date_posted'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($my_jobs as $job): ?>
                                    <tr>
                                        <td><?= e($job['title']); ?></td>
                                        <td><?= e($job['company']) ?: '-'; ?></td>
                                        <td>
                                            <span class="label label-<?= ($job['status'] == 'approved' ? 'success' : ($job['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                <?= _l('membership_job_status_' . $job['status']); ?>
                                            </span>
                                        </td>
                                        <td><?= _d($job['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

</div>
